<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use Filament\Tables; // Assuming you have this resource
use Filament\Tables\Table;
use App\Models\Requirement;
use Illuminate\Support\Str;
use App\Filament\Resources\RequirementResource;
use Filament\Widgets\TableWidget as BaseWidget;

final class ActionableRequirementsTable extends BaseWidget
{
    protected static ?int $sort = 9;

    protected int|string|array $columnSpan = 'full'; // Make it take full width

    protected static ?string $heading = 'Requirements Needing Action';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Requirement::query()
                    ->with(['scholar', 'scholarship']) // Eager load relationships
                    ->whereIn('status', ['rejected', 'pending_resubmission']) // Filter for specific statuses
                    ->latest('reviewed_at') // Show most recently reviewed first
                    ->limit(15)
            )
            ->columns([
                Tables\Columns\TextColumn::make('scholar.full_name')
                    ->label('Scholar')
                    ->searchable(['scholars.first_name', 'scholars.last_name']),
                Tables\Columns\TextColumn::make('scholarship.name')
                    ->label('Scholarship')
                    ->limit(25)
                    ->tooltip(fn (Requirement $record): string => $record->scholarship?->name ?? ''),
                Tables\Columns\TextColumn::make('document_type')
                    ->label('Document')
                    ->limit(25)
                    ->tooltip(fn (Requirement $record): string => $record->document_type ?? ''),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'danger' => 'rejected',
                        'warning' => 'pending_resubmission',
                    ])
                    ->formatStateUsing(fn (string $state): string => Str::of($state)->replace('_', ' ')->title()),
                Tables\Columns\TextColumn::make('remarks')
                    ->limit(40)
                    ->tooltip(fn (Requirement $record): string => $record->remarks ?? ''),
                Tables\Columns\TextColumn::make('reviewed_at')
                    ->label('Last Reviewed')
                    ->dateTime()
                    ->sortable()
                    ->since(),
            ])
            ->actions([
                Tables\Actions\Action::make('review')
                    ->label('Review')
                    ->icon('heroicon-m-pencil-square')
                   // Link to your RequirementResource edit/view page
                    ->url(fn (Requirement $record): string => RequirementResource::getUrl('edit', ['record' => $record])),
            ])
            ->emptyStateHeading('No requirements currently need action.')
            ->description('Showing requirements marked as rejected or pending resubmission.');
    }
}
