<?php

namespace App\Filament\Resources\ScholarshipResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\RequirementResource;
use App\Filament\Resources\ScholarResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ScholarsRelationManager extends RelationManager
{
    protected static string $relationship = 'scholars';
    protected static ?string $recordTitleAttribute = 'first_name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'graduated' => 'Graduated',
                    ])
                    ->required(),
                Forms\Components\DatePicker::make('start_date')
                    ->required(),
                Forms\Components\DatePicker::make('end_date'),
                Forms\Components\Textarea::make('remarks')
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->searchable(['first_name', 'middle_name', 'last_name']),
                Tables\Columns\TextColumn::make('school.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('course'),
                Tables\Columns\TextColumn::make('year_level'),
                Tables\Columns\BadgeColumn::make('requirements_status')
                    ->label('Requirements')
                    ->getStateUsing(function ($record) {
                        return "{$record->completed_requirements}/{$record->total_requirements} Complete";
                    })
                    ->colors([

                        'success' => fn($state) => preg_match('/^(\d+)\/\1/', $state),
                        'warning' => fn($state) => true,
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('school')
                    ->relationship('school', 'name'),
                Tables\Filters\SelectFilter::make('year_level')
                    ->options([
                        1 => '1st Year',
                        2 => '2nd Year',
                        3 => '3rd Year',
                        4 => '4th Year',
                        5 => '5th Year',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->recordSelectSearchColumns(['first_name', 'middle_name', 'last_name'])
                    ->form(fn(Tables\Actions\AttachAction $action): array => [
                        Forms\Components\Select::make('recordId')
                            ->label('Scholar')
                            ->required()
                            ->searchable()
                            ->getSearchResultsUsing(function (string $search) {
                                return \App\Models\Scholar::query()
                                    ->where('status', 'active')
                                    ->where(function ($query) use ($search) {
                                        $terms = explode(' ', $search);
                                        foreach ($terms as $term) {
                                            $query->where(function ($q) use ($term) {
                                                $q->where('first_name', 'like', $term . '%')
                                                    ->orWhere('middle_name', 'like', $term . '%')
                                                    ->orWhere('last_name', 'like', $term . '%');
                                            });
                                        }
                                    })
                                    ->limit(10)
                                    ->get()
                                    ->pluck('full_name', 'id');
                            })
                            ->placeholder('Search scholars...'),
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                            ])
                            ->required(),
                        Forms\Components\DatePicker::make('start_date')
                            ->required(),
                        Forms\Components\DatePicker::make('end_date'),
                        Forms\Components\Textarea::make('remarks')
                            ->maxLength(255),
                    ])
                    ->after(function ($data, $record) {
                        $scholarship = $this->getOwnerRecord();
                        $requirements = is_array($scholarship->requirements)
                            ? $scholarship->requirements
                            : json_decode($scholarship->requirements, true);

                        foreach ($requirements as $reqType) {
                            \App\Models\Requirement::create([
                                'scholar_id' => $record->id,
                                'scholarship_id' => $scholarship->id,
                                'document_type' => $reqType,
                                'status' => 'pending',
                                'file_path' => null,
                                'submitted_at' => null
                            ]);
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make()
                    ->before(function (Model $record) {
                        $requirements = \App\Models\Requirement::where([
                            'scholar_id' => $record->id,
                            'scholarship_id' => $this->getOwnerRecord()->id
                        ])->get();

                        foreach ($requirements as $requirement) {
                            $requirement->forceDelete();
                        }
                    }),
                    Tables\Actions\Action::make('view_requirements')
                        ->label('Requirements')
                        ->icon('heroicon-o-document-text')
                        ->modalHeading(fn (Model $record) => "Requirements for {$record->full_name}")
                        ->modalDescription('View and manage scholarship requirements')
                        ->modalContent(function (Model $record) {
                            $requirements = \App\Models\Requirement::where([
                                'scholar_id' => $record->id,
                                'scholarship_id' => $this->getOwnerRecord()->id
                            ])->get();

                            return view('filament.modals.scholarship-requirements', [
                                'requirements' => $requirements,
                                'scholar' => $record,
                                'scholarship' => $this->getOwnerRecord(),
                            ]);
                        })
                        ->modalSubmitAction(false) // Remove default submit button
                        ->slideOver()
                        // ->modalCancelAction(fn (StaticAction $action) => $action->label('Close'))
                        // ->extraModalFooterActions([
                        //     Tables\Actions\Action::make('manage_requirements')
                        //         ->label('Manage Requirements')
                        //         ->icon('heroicon-m-pencil-square')
                        //         ->url(fn (Model $record) => RequirementResource::getUrl('index', [
                        //             'scholar_id' => $record->id,
                        //             'scholarship_id' => $this->getOwnerRecord()->id,
                        //         ]))
                        //         ->openUrlInNewTab(),
                        // ])

            ])
            ->bulkActions([
                Tables\Actions\DetachBulkAction::make()
                    ->before(function (Collection $records) {
                        foreach ($records as $record) {
                            $requirements = \App\Models\Requirement::where([
                                'scholar_id' => $record->id,
                                'scholarship_id' => $this->getOwnerRecord()->id
                            ])->get();

                            foreach ($requirements as $requirement) {
                                $requirement->forceDelete();
                            }
                        }
                    }),
            ]);
    }
}
