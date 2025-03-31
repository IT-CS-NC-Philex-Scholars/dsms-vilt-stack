<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnnouncementResource\Pages;
// use App\Filament\Resources\AnnouncementResource\RelationManagers; // Keep commented if no relations
use App\Models\Announcement;
use Carbon\CarbonInterface;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon; // Import Carbon for date comparison
use Filament\Tables\Filters\TrashedFilter; // Import TrashedFilter

class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    // Optional: Group in the navigation sidebar
    protected static ?string $navigationGroup = 'Content Management';

    // Optional: Define a sort order in the navigation group
    protected static ?int $navigationSort = 1;

    // Optional: Customize the model label
    protected static ?string $modelLabel = 'Announcement';
    protected static ?string $pluralModelLabel = 'Announcements';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Announcement Details')
                    ->description('Fill in the main details of the announcement.')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(debounce: 500) // Optional: Update slug on typing
                            ->columnSpanFull(), // Take full width in this section

                        Forms\Components\RichEditor::make('content')
                            ->required()
                            ->columnSpanFull() // Take full width
                            ->toolbarButtons([ // Customize the toolbar if needed
                                'attachFiles',
                                'blockquote',
                                'bold',
                                'bulletList',
                                'codeBlock',
                                'h2',
                                'h3',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'underline',
                                'undo',
                            ]),
                    ])->columns(1), // Use 1 column layout for this section

                Forms\Components\Section::make('Settings')
                    ->description('Configure priority and publication schedule.')
                    ->schema([
                        Forms\Components\Select::make('priority')
                            ->options([
                                'low' => 'Low',
                                'medium' => 'Medium',
                                'high' => 'High',
                            ])
                            ->required()
                            ->default('medium')
                            ->native(false), // Use Filament's styled select dropdown

                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('Publish Date & Time')
                            ->helperText('Leave blank to keep as draft (unpublished). Sets the time when the announcement becomes visible.')
                            ->native(false) // Use Filament's date/time picker
                            ->default(now()) // Default to current time for convenience
                            ->nullable(), // Allow it to be empty (draft)
                    ])->columns(2), // Use 2 columns for this section
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(50) // Limit title length in table
                    ->tooltip(fn (Announcement $record): string => $record->title), // Show full title on hover

                Tables\Columns\BadgeColumn::make('priority')
                    ->colors([
                        'warning' => 'medium',
                        'success' => 'low',
                        'danger' => 'high',
                    ])
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)) // Capitalize 'low', 'medium', 'high'
                    ->sortable(),

                    Tables\Columns\IconColumn::make('is_published')
                                       ->label('Status')
                                       ->boolean()
                                       ->getStateUsing(function (Announcement $record): bool {
                                           // Check if published_at is set and is in the past or now
                                           // Make sure published_at is actually a Carbon instance before calling isPast()
                                           return $record->published_at instanceof \Carbon\CarbonInterface && $record->published_at->isPast();
                                       })
                                       ->trueIcon('heroicon-o-check-badge')
                                       ->falseIcon('heroicon-o-clock') // Use clock for scheduled/draft
                                       ->trueColor('success')
                                       ->falseColor('warning')
                                       ->tooltip(function (Announcement $record): string {
                                            if (!$record->published_at) return 'Draft';
                                            // Also check here if it's a Carbon instance
                                            return $record->published_at instanceof \Carbon\CarbonInterface && $record->published_at->isPast() ? 'Published' : 'Scheduled';
                                       }),


                                       Tables\Columns\TextColumn::make('published_at')
                                                          ->label('Publish Date')
                                                          ->dateTime() // Keep this, Filament handles basic formatting well
                                                          ->sortable()
                                                          // ---- Use CarbonInterface here ----
                                                          ->formatStateUsing(fn (?CarbonInterface $state): string => $state ? $state->isoFormat('MMM D, YYYY HH:mm') : 'Draft')
                                                          // Using isoFormat for potentially better localization support, but 'M d, Y H:i' is fine too.
                                                          ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true), // Hide by default

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true), // Hide by default

                Tables\Columns\TextColumn::make('deleted_at')
                     ->label('Deleted On')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true), // Hide by default
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('priority')
                    ->options([
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                    ])
                    ->native(false),

                Tables\Filters\TernaryFilter::make('published_status')
                    ->label('Publication Status')
                    ->placeholder('All')
                    ->trueLabel('Published')
                    ->falseLabel('Draft / Scheduled')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('published_at')->where('published_at', '<=', now()),
                        false: fn (Builder $query) => $query->whereNull('published_at')->orWhere('published_at', '>', now()),
                        blank: fn (Builder $query) => $query, // No filter if 'All' is selected
                    )
                    ->native(false),

                TrashedFilter::make(), // Adds filter for viewing soft-deleted records
            ])
            ->actions([
                Tables\Actions\EditAction::make()->iconButton(), // Use icon buttons for cleaner look
                Tables\Actions\DeleteAction::make()->iconButton(),
                Tables\Actions\RestoreAction::make()->iconButton(), // Action to restore soft-deleted records
                Tables\Actions\ForceDeleteAction::make()->iconButton(), // Action to permanently delete records
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(), // Bulk restore
                    Tables\Actions\ForceDeleteBulkAction::make(), // Bulk force delete
                ]),
            ])
            ->defaultSort('published_at', 'desc'); // Default sort by publish date descending
    }

    public static function getRelations(): array
    {
        return [
            // Define relation managers here if needed in the future
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAnnouncements::route('/'),
            'create' => Pages\CreateAnnouncement::route('/create'),
            'edit' => Pages\EditAnnouncement::route('/{record}/edit'),
        ];
    }

    // This is important for Soft Deletes to work correctly with Filament tables/filters
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
