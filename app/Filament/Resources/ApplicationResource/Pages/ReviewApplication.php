<?php

declare(strict_types=1);

namespace App\Filament\Resources\ApplicationResource\Pages;

use App\Filament\Resources\ApplicationResource;
use Filament\Resources\Pages\Page;
use App\Models\Application;
use App\Models\Document;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Actions;

final class ReviewApplication extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = ApplicationResource::class;

    protected static string $view = 'filament.resources.application-resource.pages.review-application';

    public ?Application $record = null;

    public function mount(Application $record): void
    {
        $this->record = $record;
        static::authorizeResourceAccess();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Document::query()->where('application_id', $this->record->id))
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label('Document Type')
                    ->formatStateUsing(fn (string $state) => str_replace('_', ' ', ucwords($state)))
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('original_name')
                    ->label('File Name')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Uploaded')
                    ->dateTime()
                    ->sortable(),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'danger' => 'rejected',
                        'warning' => 'pending',
                        'success' => 'approved',
                    ])
                    ->default('pending'),
                
                Tables\Columns\TextColumn::make('semester_display')
                    ->label('Semester')
                    ->html()
                    ->formatStateUsing(function ($record) {
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
                
                Tables\Filters\Filter::make('has_semester')
                    ->label('Has Semester Info')
                    ->query(fn (Builder $query) => $query->whereNotNull('semester_type')->whereNotNull('semester_number')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->modalContent(fn (Document $record) => view('filament.modals.document-view', ['document' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false),
                
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Document $record) {
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
                    ->action(function (Document $record, array $data) {
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
                        $records->each(function (Document $record) {
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
                        $records->each(function (Document $record) use ($data) {
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

    public function updateApplicationStatus(string $status): void
    {
        $this->record->update([
            'status' => $status,
        ]);
        
        $statusLabels = [
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'needs_information' => 'Needs More Information',
            'pending_review' => 'Under Review',
        ];
        
        $label = $statusLabels[$status] ?? ucfirst($status);
        
        Notification::make()
            ->title("Application {$label}")
            ->success()
            ->send();
            
        // Redirect to the edit page for further adjustments if needed
        $this->redirect(ApplicationResource::getUrl('edit', ['record' => $this->record]));
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('approveApplication')
                ->label('Approve')
                ->color('success')
                ->icon('heroicon-m-check-circle')
                ->requiresConfirmation()
                ->modalDescription('Are you sure you want to APPROVE this application? The applicant will be notified.')
                ->action(fn () => $this->updateApplicationStatus('approved')),
            
            Actions\Action::make('rejectApplication')
                ->label('Reject')
                ->color('danger')
                ->icon('heroicon-m-x-circle')
                ->requiresConfirmation()
                ->modalDescription('Are you sure you want to REJECT this application? You will be redirected to provide a reason. The applicant will be notified.')
                ->action(fn () => $this->updateApplicationStatus('rejected')),
            
            Actions\Action::make('needsInformation')
                ->label('Needs Info')
                ->color('warning')
                ->icon('heroicon-m-exclamation-triangle')
                ->requiresConfirmation()
                ->modalDescription('Mark application as needing MORE INFORMATION? The applicant will be notified.')
                ->action(fn () => $this->updateApplicationStatus('needs_information')),
            
            Actions\Action::make('markPendingReview')
                ->label('Mark Pending')
                ->color('gray')
                ->icon('heroicon-m-arrow-path')
                ->requiresConfirmation()
                ->modalDescription('Mark application as PENDING REVIEW?')
                ->action(fn () => $this->updateApplicationStatus('pending_review')),
        ];
    }
} 