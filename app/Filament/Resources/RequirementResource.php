<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RequirementResource\Pages;
use App\Filament\Resources\RequirementResource\RelationManagers;
use App\Models\Requirement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RequirementResource extends Resource
{
    protected static ?string $model = Requirement::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
        protected static ?string $navigationGroup = 'Scholarship Management';

        public static function form(Form $form): Form
            {
                return $form
                    ->schema([
                        Forms\Components\Card::make()
                            ->schema([
                                Forms\Components\Select::make('scholar_id')
                                    ->relationship('scholar', 'first_name')
                                    ->required(),
                                Forms\Components\Select::make('scholarship_id')
                                    ->relationship('scholarships', 'name')
                                    ->required(),
                                Forms\Components\TextInput::make('document_type')
                                    ->required(),
                                Forms\Components\FileUpload::make('file_path')
                                    ->required()
                                    ->disk('public')
                                    ->directory('requirements'),
                                Forms\Components\Select::make('status')
                                    ->options([
                                        'pending' => 'Pending',
                                        'approved' => 'Approved',
                                        'rejected' => 'Rejected',
                                    ])
                                    ->required(),
                                Forms\Components\Textarea::make('remarks'),
                            ])
                    ]);
            }
            public static function table(Table $table): Table
                {
                    return $table
                        ->columns([
                            Tables\Columns\TextColumn::make('scholar.full_name'),
                            Tables\Columns\TextColumn::make('scholarships.name'),
                            Tables\Columns\TextColumn::make('document_type'),
                            Tables\Columns\BadgeColumn::make('status')
                                ->colors([
                                    'warning' => 'pending',
                                    'success' => 'approved',
                                    'danger' => 'rejected',
                                ]),
                            Tables\Columns\TextColumn::make('submitted_at')
                                ->dateTime(),
                        ])
                        ->filters([
                            Tables\Filters\SelectFilter::make('status')
                                ->options([
                                    'pending' => 'Pending',
                                    'approved' => 'Approved',
                                    'rejected' => 'Rejected',
                                ]),
                        ])
                        ->actions([
                            Tables\Actions\EditAction::make(),
                            Tables\Actions\Action::make('verify')
                                ->form([
                                    Forms\Components\Select::make('status')
                                        ->options([
                                            'approved' => 'Approved',
                                            'rejected' => 'Rejected',
                                        ])
                                        ->required(),
                                    Forms\Components\Textarea::make('remarks')
                                        ->required(),
                                ])
                                ->action(function (Requirement $record, array $data): void {
                                    $record->update([
                                        'status' => $data['status'],
                                        'remarks' => $data['remarks'],
                                        'reviewed_at' => now(),
                                    ]);
                                })
                                ->visible(fn (Requirement $record) => $record->status === 'pending'),
                        ]);
                }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRequirements::route('/'),
            'create' => Pages\CreateRequirement::route('/create'),
            'edit' => Pages\EditRequirement::route('/{record}/edit'),
        ];
    }
}
