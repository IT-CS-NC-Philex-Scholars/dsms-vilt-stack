<?php

declare(strict_types=1);

namespace App\Filament\Resources\ApplicationResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Notification;

final class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    protected static ?string $recordTitleAttribute = 'type';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('type')
                    ->required()
                    ->maxLength(255)
                    ->disabled(),
                
                Forms\Components\TextInput::make('original_name')
                    ->label('File Name')
                    ->disabled(),
                
                Forms\Components\Select::make('status')
                    ->label('Review Status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->required(),
                
                Forms\Components\Toggle::make('verified')
                    ->label('Mark as Verified')
                    ->helperText('Toggle to mark this document as verified.')
                    ->default(false),
                
                Forms\Components\Textarea::make('notes')
                    ->label('Review Notes')
                    ->rows(3)
                    ->maxLength(500)
                    ->helperText('Add notes about this document, especially if rejected.'),
                
                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\Select::make('semester_type')
                            ->label('Semester Type')
                            ->options([
                                'semestral' => 'Semestral (2 semesters per year)',
                                'trimesteral' => 'Trimesteral (3 trimesters per year)',
                            ])
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('semester_number')
                            ->label('Semester Number')
                            ->numeric()
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('academic_year')
                            ->label('Academic Year')
                            ->numeric()
                            ->disabled(),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('type')
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label('Document Type')
                    ->formatStateUsing(fn (string $state) => str_replace('_', ' ', ucwords($state)))
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('original_name')
                    ->label('File Name')
                    ->searchable()
                    ->limit(30),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'danger' => 'rejected',
                        'warning' => 'pending',
                        'success' => 'approved',
                    ])
                    ->default('pending')
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('verified')
                    ->boolean()
                    ->label('Verified')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Uploaded')
                    ->dateTime()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('verification_date')
                    ->label('Reviewed On')
                    ->dateTime()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('semester_display')
                    ->label('Semester')
                    ->html()
                    ->getStateUsing(function ($record) {
                        if (!$record->semester_type || !$record->semester_number) {
                            return new \Illuminate\Support\HtmlString('<span class="text-xs text-gray-400 dark:text-gray-500">N/A</span>');
                        }
                        
                        $year = $record->academic_year ? $record->academic_year . '-' . ($record->academic_year + 1) : '';
                        $semesterText = '';
                        if ($record->semester_type === 'semestral') {
                            $semesterText = $record->semester_number === 1 ? '1st Semester' : '2nd Semester';
                        } else { // trimesteral
                            $semesterText = match ($record->semester_number) {
                                1 => '1st Trimester',
                                2 => '2nd Trimester',
                                3 => '3rd Trimester',
                                default => $record->semester_number . 'th Trimester'
                            };
                        }
                        
                        return $year ? "{$semesterText} <span class='text-xs text-gray-500'>({$year})</span>" : $semesterText;
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
                
                Tables\Filters\TernaryFilter::make('verified')
                    ->label('Verification Status')
                    ->placeholder('All Documents')
                    ->trueLabel('Verified Documents')
                    ->falseLabel('Unverified Documents')
                    ->queries(
                        true: fn (Builder $query) => $query->where('verified', true),
                        false: fn (Builder $query) => $query->where('verified', false),
                        blank: fn (Builder $query) => $query,
                    ),
                
                Tables\Filters\Filter::make('has_semester')
                    ->label('Has Semester Info')
                    ->query(fn (Builder $query) => $query->whereNotNull('semester_type')->whereNotNull('semester_number')),
            ])
            ->headerActions([
                // No add action as documents should be uploaded by scholars
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->modalContent(fn ($record) => view('filament.modals.document-view', ['document' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false),
                
                Tables\Actions\EditAction::make(),
                
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'approved',
                            'verified' => true,
                            'verification_date' => now(),
                        ]);
                        
                        Notification::make()
                            ->title('Document Approved')
                            ->success()
                            ->send();
                    }),
                
                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('notes')
                            ->label('Rejection Reason')
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'verified' => false,
                            'notes' => $data['notes'],
                            'verification_date' => now(),
                        ]);
                        
                        Notification::make()
                            ->title('Document Rejected')
                            ->danger()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('approve_all')
                    ->label('Approve Selected')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function (\Illuminate\Support\Collection $records) {
                        $records->each(function ($record) {
                            $record->update([
                                'status' => 'approved',
                                'verified' => true,
                                'verification_date' => now(),
                            ]);
                        });
                        
                        Notification::make()
                            ->title('Documents Approved')
                            ->success()
                            ->send();
                    }),
                
                Tables\Actions\BulkAction::make('reject_all')
                    ->label('Reject Selected')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('notes')
                            ->label('Rejection Reason')
                            ->required(),
                    ])
                    ->action(function (\Illuminate\Support\Collection $records, array $data) {
                        $records->each(function ($record) use ($data) {
                            $record->update([
                                'status' => 'rejected',
                                'verified' => false,
                                'notes' => $data['notes'],
                                'verification_date' => now(),
                            ]);
                        });
                        
                        Notification::make()
                            ->title('Documents Rejected')
                            ->danger()
                            ->send();
                    }),
            ]);
    }
}
