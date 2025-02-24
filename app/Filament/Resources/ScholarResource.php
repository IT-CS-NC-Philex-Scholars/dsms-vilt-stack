<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\School;
use App\Models\Scholar;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ScholarResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ScholarResource\RelationManagers;
use App\Filament\Resources\ScholarResource\RelationManagers\ScholarRequirementsRelationManager;

class ScholarResource extends Resource
{
    protected static ?string $model = Scholar::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Scholars';

    protected static ?string $modelLabel = 'Scholar';

    protected static ?string $pluralModelLabel = 'Scholars';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Scholar Information')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Personal Details')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Forms\Components\Card::make()
                                    ->schema([
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\TextInput::make('first_name')
                                                    ->label('First Name')
                                                    ->required()
                                                    ->placeholder('Enter first name'),
                                                Forms\Components\TextInput::make('middle_name')
                                                    ->label('Middle Name')
                                                    ->placeholder('Enter middle name'),
                                                Forms\Components\TextInput::make('last_name')
                                                    ->label('Last Name')
                                                    ->required()
                                                    ->placeholder('Enter last name'),
                                            ]),
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\DatePicker::make('birth_date')
                                                    ->label('Birth Date')
                                                    ->required(),
                                                Forms\Components\Select::make('gender')
                                                    ->label('Gender')
                                                    ->required()
                                                    ->options([
                                                        'male' => 'Male',
                                                        'female' => 'Female',
                                                        'other' => 'Other'
                                                    ]),
                                            ]),
                                            Forms\Components\Section::make('Additional Information')
                                                ->description('Add custom fields to collect additional information about the scholar')
                                                ->schema([
                                                    Forms\Components\Builder::make('additional_details')
                                                        ->blocks([
                                                            Forms\Components\Builder\Block::make('text')
                                                                ->icon('heroicon-o-pencil')
                                                                ->label('Text Field')

                                                                ->schema([
                                                                    Forms\Components\TextInput::make('label')
                                                                        ->label('Field Label')
                                                                        ->required()
                                                                        ->placeholder('Enter field label (e.g. Nickname)'),
                                                                    Forms\Components\TextInput::make('value')
                                                                        ->label('Field Value')
                                                                        ->required()
                                                                        ->placeholder('Enter value'),
                                                                ]),
                                                            Forms\Components\Builder\Block::make('number')
                                                                // ->icon('heroicon-o-number')
                                                                ->label('Number Field')

                                                                ->schema([
                                                                    Forms\Components\TextInput::make('label')
                                                                        ->label('Field Label')
                                                                        ->required()
                                                                        ->placeholder('Enter field label (e.g. Number of Siblings)'),
                                                                    Forms\Components\TextInput::make('value')
                                                                        ->label('Field Value')
                                                                        ->numeric()
                                                                        ->required()
                                                                        ->placeholder('Enter numeric value'),
                                                                ]),
                                                            Forms\Components\Builder\Block::make('date')
                                                                ->icon('heroicon-o-calendar')
                                                                ->label('Date Field')

                                                                ->schema([
                                                                    Forms\Components\TextInput::make('label')
                                                                        ->label('Field Label')
                                                                        ->required()
                                                                        ->placeholder('Enter field label (e.g. Registration Date)'),
                                                                    Forms\Components\DatePicker::make('value')
                                                                        ->label('Field Value')
                                                                        ->required(),
                                                                ]),
                                                            Forms\Components\Builder\Block::make('select')
                                                                ->icon('heroicon-o-list-bullet')
                                                                ->label('Select Field')

                                                                ->schema([
                                                                    Forms\Components\TextInput::make('label')
                                                                        ->label('Field Label')
                                                                        ->required()
                                                                        ->placeholder('Enter field label (e.g. Preferred Language)'),
                                                                    Forms\Components\TagsInput::make('options')
                                                                        ->label('Dropdown Options')
                                                                        ->helperText('Enter options and press Enter/Return after each one')
                                                                        ->placeholder('Type an option and press Enter')
                                                                        ->required(),
                                                                    Forms\Components\Select::make('value')
                                                                        ->label('Selected Value')
                                                                        ->options(function (Forms\Get $get) {
                                                                            return collect($get('options'))->mapWithKeys(fn ($option) => [$option => $option]);
                                                                        })
                                                                        ->required(),
                                                                ]),
                                                        ])
                                                        ->blockLabels(fn () => [
                                                            'text' => 'Add Text Field',
                                                            'number' => 'Add Number Field',
                                                            'date' => 'Add Date Field',
                                                            'select' => 'Add Dropdown Field'
                                                        ])
                                                        ->collapsible()
                                                        ->cloneable()
                                                        ->reorderableWithButtons()
                                                        ->columnSpanFull(),
                                                ])->collapsible(),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Contact Information')
                            ->icon('heroicon-o-phone')
                            ->schema([
                                Forms\Components\Card::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('email')
                                            ->label('Email Address')
                                            ->email()
                                            ->required()
                                            ->placeholder('Enter email address'),
                                        Forms\Components\TextInput::make('contact_number')
                                            ->label('Contact Number')
                                            ->required()
                                            ->tel()
                                            ->placeholder('Enter contact number'),
                                        Forms\Components\Textarea::make('address')
                                            ->label('Complete Address')
                                            ->required()
                                            ->placeholder('Enter complete address'),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Academic Details')
                            ->icon('heroicon-o-academic-cap')
                            ->schema([
                                Forms\Components\Card::make()
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                            Forms\Components\Select::make('school.id')
                                                ->label('School')

                                                ->required()
                                                ->options(School::all()->pluck('name', 'id'))
                                                ->createOptionForm([
                                                    Forms\Components\TextInput::make('name')
                                                        ->label('Name')
                                                        ->required()
                                                        ->maxLength(255),
                                                    Forms\Components\TextInput::make('address')
                                                        ->label('Address')
                                                        ->required()
                                                        ->maxLength(255),
                                                    Forms\Components\TextInput::make('city')
                                                        ->label('City')
                                                        ->required()
                                                        ->maxLength(255),
                                                    Forms\Components\TextInput::make('province')
                                                        ->label('Province')
                                                        ->required()
                                                        ->maxLength(255),
                                                    Forms\Components\TextInput::make('region')
                                                        ->label('Region')
                                                        ->required()
                                                        ->maxLength(255),
                                                    Forms\Components\Select::make('type')
                                                        ->label('Type')
                                                        ->required()
                                                        ->options([
                                                            'public' => 'Public',
                                                            'private' => 'Private'
                                                        ]),
                                                    Forms\Components\Select::make('level')
                                                        ->label('Level')
                                                        ->required()
                                                        ->options([
                                                            'elementary' => 'Elementary',
                                                            'secondary' => 'Secondary',
                                                            'tertiary' => 'Tertiary'
                                                        ]),
                                                    Forms\Components\TextInput::make('contact_number')
                                                        ->label('Contact Number')
                                                        ->required()
                                                        ->maxLength(255),
                                                    Forms\Components\TextInput::make('email')
                                                        ->label('Email')
                                                        ->required()
                                                        ->email()
                                                        ->maxLength(255),
                                                    Forms\Components\TextInput::make('website')
                                                        ->label('Website')
                                                        ->maxLength(255),
                                                    Forms\Components\Textarea::make('description')
                                                        ->label('Description')
                                                        ->maxLength(65535),
                                                    Forms\Components\Toggle::make('is_active')
                                                        ->label('Active Status')
                                                        ->required(),
                                                    Forms\Components\KeyValue::make('additional_info')
                                                        ->label('Additional Information')
                                                ])
                                                ->createOptionUsing(function ($data) {
                                                    return School::create($data);
                                                })
                                                ->mutateDehydratedStateUsing(function ($state) {
                                                    return School::find($state)?->id;
                                                })
                                                ->searchable()
                                                ->placeholder('Select school'),
                                                Forms\Components\Select::make('type')
                                                    ->label('Scholarship Type')
                                                    ->required()
                                                    ->options([
                                                        'High_School' => 'High School',
                                                        'Senior_High_School' => 'Senior High School',
                                                        'College' => 'College',
                                                        'Graduate' => 'Graduate'
                                                    ]),
                                            ]),
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('course')
                                                    ->label('Course/Program')
                                                    ->placeholder('Enter course or program')
                                                    ->visible(fn (Forms\Get $get) =>
                                                        in_array($get('type'), ['College'])),
                                                Forms\Components\Select::make('year_level')
                                                    ->label('Year Level')
                                                    ->required()
                                                    ->options(function (Forms\Get $get) {
                                                        if ($get('type') === 'High_School') {
                                                            return [
                                                                7 => 'Grade 7',
                                                                8 => 'Grade 8',
                                                                9 => 'Grade 9',
                                                                10 => 'Grade 10'
                                                            ];
                                                        }
                                                        if ($get('type') === 'Senior_High_School') {
                                                            return [
                                                                11 => 'Grade 11',
                                                                12 => 'Grade 12'
                                                            ];
                                                        }
                                                        if ($get('type') === 'College') {
                                                            return [
                                                                1 => '1st Year',
                                                                2 => '2nd Year',
                                                                3 => '3rd Year',
                                                                4 => '4th Year'
                                                            ];
                                                        }
                                                        return [];
                                                    })
                                                    ->visible(fn (Forms\Get $get) =>
                                                        $get('type') !== 'Graduate'),
                                            ]),
                                        Forms\Components\Select::make('status')
                                            ->label('Scholar Status')
                                            ->required()
                                            ->options([
                                                'active' => 'Active',
                                                'inactive' => 'Inactive',
                                                'graduated' => 'Graduated',
                                                'terminated' => 'Terminated'
                                            ]),

                                    ]),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Scholar Name')
                    ->searchable(['first_name', 'middle_name', 'last_name'])
                    ->sortable()
                    ->formatStateUsing(fn ($record) =>
                        "{$record->last_name}, {$record->first_name} {$record->middle_name}"),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->icon('heroicon-m-envelope'),
                Tables\Columns\TextColumn::make('contact_number')
                    ->searchable()
                    ->icon('heroicon-m-phone'),
                Tables\Columns\TextColumn::make('school.name')
                    ->searchable()
                    ->icon('heroicon-m-academic-cap'),
                Tables\Columns\BadgeColumn::make('type')
                    ->colors([
                        'success' => 'full',
                        'warning' => 'partial',
                        'primary' => 'merit',
                    ]),
                Tables\Columns\TextColumn::make('course')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'danger' => 'inactive',
                        'primary' => 'graduated',
                        'warning' => 'terminated',
                    ]),
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
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'graduated' => 'Graduated',
                        'terminated' => 'Terminated'
                    ]),
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'full' => 'Full Scholarship',
                        'partial' => 'Partial Scholarship',
                        'merit' => 'Merit-based'
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            // ScholarRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListScholars::route('/'),
            'create' => Pages\CreateScholar::route('/create'),
            'edit' => Pages\EditScholar::route('/{record}/edit'),
            ''
        ];
    }
}
