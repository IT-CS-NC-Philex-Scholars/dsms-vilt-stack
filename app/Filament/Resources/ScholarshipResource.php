<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScholarshipResource\Pages;
use App\Filament\Resources\ScholarshipResource\RelationManagers;
use App\Models\Scholarship;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ScholarshipResource extends Resource
{
    protected static ?string $model = Scholarship::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
        protected static ?string $navigationGroup = 'Scholarship Management';

        public static function form(Form $form): Form
        {
            return $form
                ->schema([
                    Forms\Components\Card::make()
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\RichEditor::make('description')
                                ->required(),
                            Forms\Components\TextInput::make('amount')
                                ->required()
                                ->numeric()
                                ->prefix('₱'),
                            Forms\Components\TagsInput::make('requirements')
                                ->required()
                                ->placeholder('Add requirements')
                                ->helperText('Press Enter to add requirements'),
                            Forms\Components\DatePicker::make('application_deadline')
                                ->required(),
                            Forms\Components\Select::make('status')
                                ->required()
                                ->options([
                                    'active' => 'Active',
                                    'inactive' => 'Inactive',
                                    'closed' => 'Closed',
                                ])
                        ])
                ]);
        }

        public static function table(Table $table): Table
        {
            return $table
                ->columns([
                    Tables\Columns\TextColumn::make('name')
                        ->searchable()
                        ->sortable(),
                    Tables\Columns\TextColumn::make('amount')
                        ->money('PHP')
                        ->sortable(),
                    Tables\Columns\TextColumn::make('application_deadline')
                        ->date()
                        ->sortable(),
                    Tables\Columns\BadgeColumn::make('status')
                        ->colors([
                            'success' => 'active',
                            'danger' => 'inactive',
                            'warning' => 'closed',
                        ]),
                    Tables\Columns\TextColumn::make('scholars_count')
                        ->counts('scholars')
                        ->label('Assigned Scholars'),
                ])
                ->filters([
                    Tables\Filters\SelectFilter::make('status')
                        ->options([
                            'active' => 'Active',
                            'inactive' => 'Inactive',
                            'closed' => 'Closed',
                        ]),
                ])
                ->actions([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make(),
                ]);
        }

        public static function getRelations(): array
        {
            return [
                RelationManagers\ScholarsRelationManager::class,
                RelationManagers\RequirementsRelationManager::class,
            ];
        }

        public static function getPages(): array
        {
            return [
                'index' => Pages\ListScholarships::route('/'),
                'create' => Pages\CreateScholarship::route('/create'),
                'edit' => Pages\EditScholarship::route('/{record}/edit'),
                'view' => Pages\ViewScholarship::route('/{record}'),
            ];
        }
    }
