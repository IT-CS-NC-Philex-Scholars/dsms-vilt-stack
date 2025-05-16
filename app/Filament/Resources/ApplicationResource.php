<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ApplicationResource\Pages;
use App\Filament\Resources\ApplicationResource\RelationManagers;
use App\Models\Application;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Filament\Infolists;
use Filament\Infolists\Infolist;

final class ApplicationResource extends Resource
{
    protected static ?string $model = Application::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationGroup = 'Scholarship Management';

    protected static ?string $navigationLabel = 'Application Reviews';

    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereIn('status', ['pending', 'submitted', 'pending_review'])
            ->count() > 0 
                ? (string) static::getModel()::whereIn('status', ['pending', 'submitted', 'pending_review'])->count()
                : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::whereIn('status', ['pending', 'submitted', 'pending_review'])->count() > 0
            ? 'warning'
            : null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Application Status')
                            ->schema([
                                Forms\Components\Select::make('status')
                    ->required()
                                    ->options([
                                        'draft' => 'Draft',
                                        'incomplete' => 'Incomplete',
                                        'submitted' => 'Submitted',
                                        'pending_review' => 'Under Review',
                                        'needs_information' => 'Needs Additional Information',
                                        'approved' => 'Approved',
                                        'rejected' => 'Rejected',
                                    ])
                                    ->default('pending_review')
                                    ->native(false)
                                    ->live()
                                    ->helperText('Changing the status will notify the applicant of the change.'),
                                
                Forms\Components\Textarea::make('rejection_reason')
                                    ->rows(3)
                                    ->visible(fn (Forms\Get $get) => $get('status') === 'rejected')
                                    ->required(fn (Forms\Get $get) => $get('status') === 'rejected')
                                    ->maxLength(500)
                                    ->helperText('If rejecting the application, please provide a reason.'),
                            ]),
                        
                        Forms\Components\Section::make('Verification')
                            ->schema([
                Forms\Components\Toggle::make('address_verified')
                                    ->onColor('success')
                                    ->offColor('danger')
                                    ->inline(false)
                                    ->helperText('Confirm address details have been verified.'),
                Forms\Components\Toggle::make('grade_verified')
                                    ->onColor('success')
                                    ->offColor('danger')
                                    ->inline(false)
                                    ->helperText('Confirm grade requirements have been verified.'),
                Forms\Components\Toggle::make('enrollment_verified')
                                    ->onColor('success')
                                    ->offColor('danger')
                                    ->inline(false)
                                    ->helperText('Confirm enrollment details have been verified.'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
                
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Application Details')
                            ->schema([
                                Forms\Components\Select::make('user_id')
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->required()
                                    ->preload()
                                    ->disabled(),
                                
                Forms\Components\Select::make('scholarship_id')
                                    ->relationship('scholarship', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->disabled(),
                                
                                Forms\Components\DateTimePicker::make('submitted_at')
                                    ->label('Submission Date')
                                    ->disabled(),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Applicant')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('scholarship.name')
                    ->label('Scholarship')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->sortable()
                    ->searchable()
                    ->colors([
                        'danger' => 'rejected',
                        'warning' => ['needs_information', 'incomplete', 'draft'],
                        'success' => 'approved',
                        'info' => ['submitted', 'pending_review', 'pending'],
                    ]),
                
                Tables\Columns\IconColumn::make('address_verified')
                    ->boolean()
                    ->label('Address')
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('grade_verified')
                    ->boolean()
                    ->label('Grades')
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('enrollment_verified')
                    ->boolean()
                    ->label('Enrollment')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('submitted_at')
                    ->dateTime('M d, Y h:i A')
                    ->placeholder('N/A')
                    ->sortable()
                    ->label('Submitted'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Update')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'incomplete' => 'Incomplete',
                        'submitted' => 'Submitted',
                        'pending_review' => 'Under Review',
                        'needs_information' => 'Needs Additional Information',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->multiple(),
                
                Tables\Filters\Filter::make('pending_verification')
                    ->query(fn (Builder $query) => $query
                        ->where('address_verified', false)
                        ->orWhere('grade_verified', false)
                        ->orWhere('enrollment_verified', false))
                    ->label('Pending Verification'),
                
                Tables\Filters\Filter::make('submitted_at')
                    ->form([
                        Forms\Components\DatePicker::make('submitted_from'),
                        Forms\Components\DatePicker::make('submitted_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['submitted_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('submitted_at', '>=', $date),
                            )
                            ->when(
                                $data['submitted_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('submitted_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('review')
                    ->label(fn (Application $record): string => match (optional($record)->status) {
                        'submitted', 'pending_review' => 'Review Application',
                        'needs_information' => 'Review & Update Info',
                        'approved', 'rejected' => 'View Reviewed Details',
                        default => 'View Documents',
                    })
                    ->icon('heroicon-o-document-magnifying-glass')
                    ->url(fn (Application $record): string => static::getUrl('review', ['record' => $record]))
                    ->color(fn (Application $record): string => match (optional($record)->status) {
                        'submitted', 'pending_review' => 'primary',
                        'needs_information' => 'warning',
                        default => 'gray',
                    })
                    ->visible(fn (Application $record): bool => in_array(optional($record)->status, ['draft', 'incomplete', 'pending', 'submitted', 'pending_review', 'needs_information', 'approved', 'rejected'], true)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check-circle')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each(function (Application $application) {
                                $application->update([
                                    'status' => 'approved',
                                    'address_verified' => true,
                                    'grade_verified' => true,
                                    'enrollment_verified' => true,
                                ]);
                            });
                        }),
                    
                    Tables\Actions\BulkAction::make('mark_needs_info')
                        ->label('Mark as Needs Info')
                        ->icon('heroicon-o-exclamation-circle')
                        ->action(function (Collection $records) {
                            $records->each(function (Application $application) {
                                $application->update(['status' => 'needs_information']);
                            });
                        }),
                    
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Applicant & Scholarship')
                    ->columns(2)
                    ->schema([
                        Infolists\Components\TextEntry::make('user.name')->label('Applicant Name'),
                        Infolists\Components\TextEntry::make('scholarship.name')->label('Scholarship'),
                        Infolists\Components\TextEntry::make('user.email')->label('Email')
                            ->copyable()
                            ->icon('heroicon-m-envelope')
                            ->columnSpanFull(),
                    ]),
                Infolists\Components\Section::make('Application Details')
                    ->columns(2)
                    ->schema([
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'approved' => 'success',
                                'submitted', 'pending_review', 'pending' => 'info',
                                'rejected' => 'danger',
                                default => 'warning',
                            }),
                        Infolists\Components\TextEntry::make('submitted_at')
                            ->label('Submitted On')
                            ->dateTime('M d, Y h:i A')
                            ->placeholder('N/A'),
                        Infolists\Components\TextEntry::make('rejection_reason')
                            ->label('Rejection Reason')
                            ->visible(fn ($record) => $record->status === 'rejected')
                            ->markdown()
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Last Update')
                            ->since()
                            ->columnSpanFull(),
                    ]),
                Infolists\Components\Section::make('Verification Checklist')
                    ->columns(3)
                    ->schema([
                        Infolists\Components\IconEntry::make('address_verified')->label('Address')->boolean(),
                        Infolists\Components\IconEntry::make('grade_verified')->label('Grades')->boolean(),
                        Infolists\Components\IconEntry::make('enrollment_verified')->label('Enrollment')->boolean(),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\DocumentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApplications::route('/'),
            'create' => Pages\CreateApplication::route('/create'),
            'view' => Pages\ViewApplication::route('/{record}'),
            'edit' => Pages\EditApplication::route('/{record}/edit'),
            'review' => Pages\ReviewApplication::route('/{record}/review'),
        ];
    }
}
