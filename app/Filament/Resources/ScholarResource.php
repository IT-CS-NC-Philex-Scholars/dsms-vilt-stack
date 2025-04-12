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
use Filament\Infolists; // <-- Add this
use Filament\Infolists\Infolist; // <-- Add this
use Illuminate\Support\HtmlString; // <-- Add this if displaying HTML
// use App\Filament\Resources\ScholarResource\RelationManagers\ScholarRequirementsRelationManager;
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
    public static function infolist(Infolist $infolist): Infolist // <-- Add this method
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
                                                ->placeholder('N/A'), // Placeholder if empty
                                            Infolists\Components\TextEntry::make('last_name')
                                                ->label('Last Name'),
                                            Infolists\Components\TextEntry::make('birth_date')
                                                ->label('Birth Date')
                                                ->date('F j, Y') // Format date nicely
                                                ->icon('heroicon-o-calendar-days'),
                                            Infolists\Components\TextEntry::make('gender')
                                                ->label('Gender')
                                                ->formatStateUsing(fn (string $state): string => ucfirst($state)) // Capitalize
                                                ->icon('heroicon-o-user'),
                                            Infolists\Components\TextEntry::make('age') // Calculated age
                                                ->label('Age')
                                                ->state(function ($record) {
                                                    return $record->birth_date?->age . ' years old';
                                                })
                                                ->icon('heroicon-o-cake'), // Cake icon for age
                                        ]),

                                    Infolists\Components\Section::make('Additional Details')
                                        ->description('Custom information provided for this scholar.')
                                        ->collapsible() // Make it collapsible if potentially long
                                        ->schema([
                                            Infolists\Components\RepeatableEntry::make('additional_details')
                                                ->label('') // Hide the main label for repeatable
                                                ->schema([
                                                    Infolists\Components\Grid::make(2) // Grid inside repeatable
                                                        ->schema([
                                                            Infolists\Components\TextEntry::make('data.label') // Access nested data
                                                                ->label('Field')
                                                                ->weight('semibold'), // Make label stand out
                                                            Infolists\Components\TextEntry::make('data.value')
                                                                ->label('Value')
                                                                ->placeholder('Not provided')
                                                                // Optional: Format based on original type if needed (requires more logic)
                                                                // ->formatStateUsing(function ($state, $get) {
                                                                //     // Need access to the 'type' key which is one level up
                                                                //     // This is complex with standard RepeatableEntry
                                                                //     // Consider a Custom Entry or ViewEntry if complex formatting needed
                                                                //     return $state;
                                                                // }),
                                                                ->copyable() // Allow copying value
                                                                ->copyableState(fn ($state) => $state) // Ensure correct state is copied
                                                        ]),
                                                    Infolists\Components\IconEntry::make('type') // Show icon based on original block type
                                                        ->label('Field Type')
                                                        ->icon(fn (string $state): string => match ($state) {
                                                            'text' => 'heroicon-o-pencil',
                                                            'number' => 'heroicon-o-hashtag', // Use same icon as form
                                                            'date' => 'heroicon-o-calendar',
                                                            'select' => 'heroicon-o-list-bullet',
                                                            default => 'heroicon-o-question-mark-circle',
                                                        })
                                                        ->alignCenter() // Align icon centrally if desired
                                                        ->columnSpanFull() // Take full width below label/value
                                                        ->hidden(true), // Often not needed to show the type explicitly, just use the icon
                                                ])
                                                ->grid(1) // Each repeatable item takes one column
                                                ->placeholder('No additional details provided.'),
                                        ])
                                        ->visible(fn ($record) => !empty($record->additional_details)), // Only show if data exists

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
                                                ->url(fn ($state): string => "mailto:{$state}", true), // Make it a mailto link
                                            Infolists\Components\TextEntry::make('contact_number')
                                                ->label('Contact Number')
                                                ->icon('heroicon-o-phone')
                                                ->copyable()
                                                ->copyableState(fn ($state) => $state)
                                                ->url(fn ($state): string => "tel:{$state}", true), // Make it a tel link
                                            Infolists\Components\TextEntry::make('address')
                                                ->label('Complete Address')
                                                ->icon('heroicon-o-map-pin')
                                                ->copyable()
                                                ->copyableState(fn ($state) => $state)
                                                ->columnSpanFull(), // Take full width
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
                                                ->icon('heroicon-o-building-office-2'), // Use a building icon
                                            Infolists\Components\TextEntry::make('type')
                                                ->label('Scholarship Level')
                                                ->formatStateUsing(fn (?string $state): string =>
                                                    ucwords(str_replace('_', ' ', $state ?? 'N/A')) // Format 'High_School' to 'High School'
                                                ),
                                            Infolists\Components\TextEntry::make('course')
                                                ->label('Course/Program')
                                                ->placeholder('N/A')
                                                ->visible(fn ($record) => in_array($record->type, ['College'])),
                                            Infolists\Components\TextEntry::make('year_level')
                                                ->label('Year/Grade Level')
                                                ->placeholder('N/A')
                                                ->formatStateUsing(function ($state, $record) {
                                                    if (empty($state)) return 'N/A';

                                                    $type = $record->type;
                                                    if ($type === 'High_School') {
                                                        return 'Grade ' . $state;
                                                    }
                                                    if ($type === 'Senior_High_School') {
                                                        return 'Grade ' . $state;
                                                    }
                                                    if ($type === 'College') {
                                                        $suffix = match ($state) {
                                                            1 => 'st',
                                                            2 => 'nd',
                                                            3 => 'rd',
                                                            default => 'th',
                                                        };
                                                        return $state . $suffix . ' Year';
                                                    }
                                                    return $state; // Fallback
                                                })
                                                ->visible(fn ($record) => in_array($record->type, ['High_School', 'Senior_High_School', 'College'])),
                                            Infolists\Components\TextEntry::make('status')
                                                ->label('Scholar Status')
                                                ->badge()
                                                ->colors([
                                                    'success' => 'active',
                                                    'danger' => 'inactive',
                                                    'primary' => 'graduated',
                                                    'warning' => 'terminated',
                                                ])
                                                ->columnSpanFull(), // Span full width at the bottom of this section
                                        ]),
                                ]),

                            // Optional: Tab for Meta Info
                            Infolists\Components\Tabs\Tab::make('System Information')
                                ->icon('heroicon-o-information-circle')
                                ->schema([
                                    Infolists\Components\Section::make()
                                        ->schema([
                                             Infolists\Components\TextEntry::make('user.name') // Assuming you have user relationship setup
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
                                        ])->columns(1), // Single column for meta info
                                ]),
                        ])->columnSpanFull(), // Make tabs take full width
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
            'view' => Pages\ViewScholar::route('/{record}'),
        ];
    }
}
