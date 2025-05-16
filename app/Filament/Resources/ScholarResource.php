<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\School;
use App\Models\Scholar;
use Filament\Infolists;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ScholarResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ScholarshipResource\RelationManagers\ScholarsRelationManager;
use App\Filament\Resources\ScholarResource\RelationManagers\ScholarRequirementsRelationManager;

final class ScholarResource extends Resource
{
    protected static ?string $model = Scholar::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Scholars';

    protected static ?string $modelLabel = 'Scholar';

    protected static ?string $pluralModelLabel = 'Scholars';

    protected static ?string $navigationGroup = 'Scholar Management';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Personal Information')
                    ->columns(2)
                            ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),
                                                Forms\Components\TextInput::make('first_name')
                                                    ->required()
                            ->maxLength(255),
                                                Forms\Components\TextInput::make('middle_name')
                            ->maxLength(255),
                                                Forms\Components\TextInput::make('last_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                                                    ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('contact_number')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('birth_date'),
                                                Forms\Components\Select::make('gender')
                                                    ->options([
                                                        'male' => 'Male',
                                                        'female' => 'Female',
                                                        'other' => 'Other',
                                'prefer_not_to_say' => 'Prefer not to say',
                            ]),
                                        Forms\Components\Textarea::make('address')
                            ->columnSpanFull()
                            ->maxLength(65535),
                    ]),
                Forms\Components\Section::make('Academic Information')
                    ->columns(2)
                            ->schema([
                        Forms\Components\Select::make('school_id')
                            ->relationship('school', 'name')
                            ->searchable()
                            ->preload()
                                                    ->createOptionForm([
                                Forms\Components\TextInput::make('name')->required()->maxLength(255),
                                Forms\Components\TextInput::make('type')
                                   ->label('School Type')
                                   ->helperText("e.g., 'shs' or 'college'")
                                   ->maxLength(50),
                                // Add other quick school fields if necessary
                            ])
                            ->helperText('The school the scholar is primarily associated with.'),
                        Forms\Components\Select::make('type') // This is scholar's educational level
                            ->label('Scholar Level')
                                                            ->options([
                                'shs' => 'Senior High School',
                                'college' => 'College',
                            ])
                                                    ->required()
                            ->helperText('The current educational level of the scholar.'),
                                                Forms\Components\TextInput::make('course')
                                                    ->label('Course/Program')
                            ->helperText('Applicable if scholar level is College.')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('year_level')
                                                    ->label('Year Level')
                            ->helperText('Applicable if scholar level is College.')
                            ->numeric(),
                                        Forms\Components\Select::make('status')
                                            ->options([
                                                'active' => 'Active',
                                                'inactive' => 'Inactive',
                                                'graduated' => 'Graduated',
                                'dropped' => 'Dropped Out',
                                'on_leave' => 'On Leave',
                            ])
                            ->default('active')
                            ->required(),
                    ]),
                Forms\Components\Section::make('Additional Details')
                    ->schema([
                        Forms\Components\KeyValue::make('additional_details')
                             ->label('Other Information')
                            ->helperText('Store any other relevant information as key-value pairs.'),
                    ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Tabs::make('Scholar Details')
                    ->tabs([
                        Infolists\Components\Tabs\Tab::make('Personal Information')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Infolists\Components\Section::make('Basic Details')
                                    ->columns(3)
                                    ->schema([
                                        Infolists\Components\TextEntry::make('first_name')
                                            ->label('First Name'),
                                        Infolists\Components\TextEntry::make('middle_name')
                                            ->label('Middle Name')
                                            ->placeholder('N/A'),
                                        Infolists\Components\TextEntry::make('last_name')
                                            ->label('Last Name'),
                                        Infolists\Components\TextEntry::make('birth_date')
                                            ->label('Birth Date')
                                            ->date('F j, Y')
                                            ->icon('heroicon-o-calendar-days'),
                                        Infolists\Components\TextEntry::make('gender')
                                            ->label('Gender')
                                            ->formatStateUsing(fn (string $state): string => ucfirst($state))
                                            ->icon('heroicon-o-user'),
                                        Infolists\Components\TextEntry::make('age')
                                            ->label('Age')
                                            ->state(fn($record): string => $record->birth_date?->age.' years old')
                                            ->icon('heroicon-o-cake'),
                                    ]),

                                Infolists\Components\Section::make('Additional Details')
                                    ->description('Custom information provided for this scholar.')
                                    ->collapsible()
                                    ->schema([
                                            Infolists\Components\RepeatableEntry::make('additional_details')
                                            ->label('')
                                            ->schema([
                                                Infolists\Components\Grid::make(2)
                                                    ->schema([
                                                        Infolists\Components\TextEntry::make('data.label')
                                                            ->label('Field')
                                                            ->weight('semibold'),
                                                        Infolists\Components\TextEntry::make('data.value')
                                                            ->label('Value')
                                                            ->placeholder('Not provided')
                                                            ->copyable()
                                                            ->copyableState(fn ($state) => $state),
                                                    ]),
                                                Infolists\Components\IconEntry::make('type')
                                                    ->label('Field Type')
                                                    ->icon(fn (string $state): string => match ($state) {
                                                        'text' => 'heroicon-o-pencil',
                                                        'number' => 'heroicon-o-hashtag',
                                                        'date' => 'heroicon-o-calendar',
                                                        'select' => 'heroicon-o-list-bullet',
                                                        default => 'heroicon-o-question-mark-circle',
                                                    })
                                                    ->alignCenter()
                                                    ->columnSpanFull()
                                                    ->hidden(true),
                                            ])
                                            ->grid(1)
                                            ->placeholder('No additional details provided.'),
                                        ])
                                    ->visible(fn ($record): bool => ! empty($record->additional_details)),

                            ]),

                        Infolists\Components\Tabs\Tab::make('Contact Information')
                            ->icon('heroicon-o-phone')
                            ->schema([
                                    Infolists\Components\Section::make()
                                        ->schema([
                                            Infolists\Components\TextEntry::make('email')
                                                ->label('Email Address')
                                                ->icon('heroicon-o-envelope')
                                                ->copyable()
                                                ->copyableState(fn ($state) => $state)
                                                ->url(fn ($state): string => "mailto:{$state}", true),
                                            Infolists\Components\TextEntry::make('contact_number')
                                                ->label('Contact Number')
                                                ->icon('heroicon-o-phone')
                                                ->copyable()
                                                ->copyableState(fn ($state) => $state)
                                                ->url(fn ($state): string => "tel:{$state}", true),
                                            Infolists\Components\TextEntry::make('address')
                                                ->label('Complete Address')
                                                ->icon('heroicon-o-map-pin')
                                                ->copyable()
                                                ->copyableState(fn ($state) => $state)
                                                ->columnSpanFull(),
                                        ])->columns(2),
                                ]),

                        Infolists\Components\Tabs\Tab::make('Academic Details')
                            ->icon('heroicon-o-academic-cap')
                            ->schema([
                                    Infolists\Components\Section::make()
                                        ->columns(2)
                                        ->schema([
                                            Infolists\Components\TextEntry::make('school.name')
                                                ->label('School')
                                                ->icon('heroicon-o-building-office-2'),
                                            Infolists\Components\TextEntry::make('type')
                                                ->label('Scholarship Level')
                                                ->formatStateUsing(fn (?string $state): string => ucwords(str_replace('_', ' ', $state ?? 'N/A'))),
                                            Infolists\Components\TextEntry::make('course')
                                                ->label('Course/Program')
                                                ->placeholder('N/A')
                                                ->visible(fn ($record): bool => $record->type == 'college'),
                                            Infolists\Components\TextEntry::make('year_level')
                                                ->label('Year/Grade Level')
                                                ->placeholder('N/A')
                                                ->formatStateUsing(function (?string $state, $record): string {
                                                    if ($state === null || $state === '' || $state === '0') {
                                                        return 'N/A';
                                                    }

                                                    $type = $record->type;
                                                    if ($type === 'shs') {
                                                        return 'Grade '.$state;
                                                    }

                                                    if ($type === 'college') {
                                                        $suffix = match ($state) {
                                                            1 => 'st',
                                                            2 => 'nd',
                                                            3 => 'rd',
                                                            default => 'th',
                                                        };

                                                        return $state.$suffix.' Year';
                                                    }

                                                    return $state;
                                                })
                                                ->visible(fn ($record): bool => in_array($record->type, ['shs', 'college'])),
                                            Infolists\Components\TextEntry::make('status')
                                                ->label('Scholar Status')
                                                ->badge()
                                                ->colors([
                                                    'success' => 'active',
                                                    'danger' => 'inactive',
                                                    'primary' => 'graduated',
                                                    'warning' => 'terminated',
                                                ])
                                                ->columnSpanFull(),
                                        ]),
                                ]),

                        Infolists\Components\Tabs\Tab::make('System Information')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                    Infolists\Components\Section::make()
                                        ->schema([
                                            Infolists\Components\TextEntry::make('user.name')
                                                ->label('Associated User Account')
                                                ->placeholder('No linked user')
                                                ->icon('heroicon-o-user-circle'),
                                            Infolists\Components\TextEntry::make('created_at')
                                                ->label('Record Created')
                                                ->dateTime('M d, Y H:i A')
                                                ->icon('heroicon-o-calendar'),
                                            Infolists\Components\TextEntry::make('updated_at')
                                                ->label('Last Updated')
                                                ->dateTime('M d, Y H:i A')
                                                ->icon('heroicon-o-clock'),
                                        ])->columns(1),
                                ]),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User Account')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('first_name')->searchable(),
                Tables\Columns\TextColumn::make('last_name')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('school.name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('type')->label('Level')->badge(),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('school_id')
                    ->label('School')
                    ->relationship('school', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'graduated' => 'Graduated',
                        'dropped' => 'Dropped Out',
                        'on_leave' => 'On Leave',
                    ]),
                Tables\Filters\SelectFilter::make('type')
                    ->label('Scholar Level')
                    ->options([
                        'shs' => 'Senior High School',
                        'college' => 'College',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ScholarRequirementsRelationManager::class,
            ScholarsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListScholars::route('/'),
            'create' => Pages\CreateScholar::route('/create'),
            'view' => Pages\ViewScholar::route('/{record}'),
            'edit' => Pages\EditScholar::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['user', 'school']);
    }
}
