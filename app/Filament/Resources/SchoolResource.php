<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\School;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use App\Filament\Resources\SchoolResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;

final class SchoolResource extends Resource
{
    protected static ?string $model = School::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    // Optional: Group in the navigation sidebar
    protected static ?string $navigationGroup = 'Core Data';

    // Optional: Define a sort order in the navigation group
    protected static ?int $navigationSort = 1;

    // Optional: Customize the model label
    protected static ?string $modelLabel = 'School';

    protected static ?string $pluralModelLabel = 'Schools';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(), // Take full width in this section

                        Select::make('type')
                            ->options([
                                // Add your actual school types here
                                'public' => 'Public',
                                'private' => 'Private',
                                'charter' => 'Charter',
                                'international' => 'International',
                            ])
                            ->required()
                            ->native(false),

                        Select::make('level')
                            ->options([
                                // Add your actual school levels here
                                'preschool' => 'Preschool',
                                'elementary' => 'Elementary',
                                'middle_school' => 'Middle School',
                                'high_school' => 'High School',
                                'k-12' => 'K-12',
                                'vocational' => 'Vocational',
                                'college' => 'College',
                                'university' => 'University',
                            ])
                            ->required()
                            ->native(false),
                    ]),

                Forms\Components\Section::make('Location')
                    ->columns(2)
                    ->schema([
                        Textarea::make('address') // Textarea might be better for multi-line addresses
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),

                        TextInput::make('city')
                            ->required()
                            ->maxLength(100),

                        TextInput::make('province') // Or State depending on your context
                            ->required()
                            ->maxLength(100),

                        // You might replace Region with a Select if you have predefined regions
                        TextInput::make('region')
                            ->maxLength(100)
                            ->nullable(),

                        // Consider adding Postal Code if relevant
                        // TextInput::make('postal_code')->maxLength(20)->nullable(),
                    ]),

                Forms\Components\Section::make('Contact & Details')
                    ->columns(2)
                    ->schema([
                        TextInput::make('contact_number')
                            ->tel() // Use telephone input type
                            ->maxLength(50)
                            ->nullable(),

                        TextInput::make('email')
                            ->email() // Use email input type with validation
                            ->maxLength(255)
                            ->nullable(),

                        TextInput::make('website')
                            ->url() // Use URL input type with validation
                            ->prefix('https://') // Helpful prefix
                            ->maxLength(255)
                            ->nullable(),

                        Toggle::make('is_active')
                            ->label('Active Status')
                            ->helperText('Inactive schools might be hidden in some parts of the application.')
                            ->default(true)
                            ->onIcon('heroicon-s-check-circle')
                            ->offIcon('heroicon-s-x-circle')
                            ->onColor('success')
                            ->offColor('danger'),
                    ]),

                Forms\Components\Section::make('Additional Information')
                    ->collapsible() // Make it collapsible if it's less frequently used
                    ->schema([
                        Textarea::make('description')
                            ->rows(5)
                            ->nullable()
                            ->columnSpanFull(),

                        KeyValue::make('additional_info')
                            ->label('Custom Fields')
                            ->helperText('Add any extra key-value data specific to this school.')
                            ->keyLabel('Field Name')
                            ->valueLabel('Field Value')
                            ->reorderable() // Allow reordering pairs
                            ->addActionLabel('Add Custom Field')
                            ->columnSpanFull(),
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->tooltip(fn (School $record): string => $record->name),

                TextColumn::make('city')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('province') // Or State
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('type')
                    ->formatStateUsing(fn (string $state): string => ucfirst(str_replace('_', ' ', $state))) // Format 'high_school' to 'High school'
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('level')
                    ->formatStateUsing(fn (string $state): string => ucfirst(str_replace('_', ' ', $state)))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('contact_number')
                    ->toggleable(isToggledHiddenByDefault: true), // Hidden by default

                TextColumn::make('email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true), // Hidden by default

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true), // Hide by default

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true), // Hide by default

                TextColumn::make('deleted_at')
                    ->label('Deleted On')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true), // Hide by default
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        // Reuse options from form or define explicitly
                        'public' => 'Public',
                        'private' => 'Private',
                        'charter' => 'Charter',
                        'international' => 'International',
                    ])
                    ->native(false),

                SelectFilter::make('level')
                    ->options([
                        // Reuse options from form or define explicitly
                        'preschool' => 'Preschool',
                        'elementary' => 'Elementary',
                        'middle_school' => 'Middle School',
                        'high_school' => 'High School',
                        'k-12' => 'K-12',
                        'vocational' => 'Vocational',
                        'college' => 'College',
                        'university' => 'University',
                    ])
                    ->multiple() // Allow filtering by multiple levels
                    ->native(false),

                // Example filter for province - useful if you have many schools
                SelectFilter::make('province')
                    ->options(fn (): array => School::query()->pluck('province', 'province')->unique()->all())
                    ->searchable()
                    ->native(false),

                TernaryFilter::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueLabel('Active')
                    ->falseLabel('Inactive')
                    ->native(false),

                TrashedFilter::make(), // Filter for soft-deleted records
            ])
            ->actions([
                Tables\Actions\EditAction::make()->iconButton(),
                Tables\Actions\DeleteAction::make()->iconButton(),
                Tables\Actions\RestoreAction::make()->iconButton(),
                Tables\Actions\ForceDeleteAction::make()->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name', 'asc'); // Default sort by school name
    }

    public static function getRelations(): array
    {
        return [
            // Define relation managers here if needed (e.g., StudentsRelationManager, TeachersRelationManager)
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSchools::route('/'),
            'create' => Pages\CreateSchool::route('/create'),
            'edit' => Pages\EditSchool::route('/{record}/edit'),
        ];
    }

    // Required for TrashedFilter and Restore/ForceDelete actions to work correctly
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
