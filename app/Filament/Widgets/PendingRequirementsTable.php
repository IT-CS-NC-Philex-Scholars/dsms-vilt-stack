<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use Filament\Tables; // If you have a resource
use Filament\Tables\Table;
use App\Models\Requirement;
use App\Filament\Resources\RequirementResource;
use Filament\Widgets\TableWidget as BaseWidget;

final class PendingRequirementsTable extends BaseWidget
{
    protected static ?int $sort = 4; // Controls order on dashboard

    protected int|string|array $columnSpan = 'full'; // Take full width

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Requirement::query()
                    ->whereIn('status', ['pending', 'submitted']) // Adjust statuses as needed
                    ->with(['scholar', 'scholarship']) // Eager load relationships
                    ->latest('submitted_at') // Show most recent first
                    ->limit(10) // Limit rows for dashboard view
            )
            ->heading('Pending Requirement Submissions')
            ->columns([
                Tables\Columns\TextColumn::make('scholar.full_name') // Uses the accessor
                    ->label('Scholar')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('scholarship.name')
                    ->label('Scholarship')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('document_type')
                    ->label('Document Type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'warning' => fn ($state): bool => in_array($state, ['pending', 'submitted']),
                    ])
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                Tables\Columns\TextColumn::make('submitted_at')
                    ->label('Submitted On')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                // Optional: Add a quick link to view/edit the requirement
                Tables\Actions\Action::make('view')
                    ->label('View')
                    ->icon('heroicon-m-eye'),
                // ->url(fn (Requirement $record): string => RequirementResource::getUrl('edit', ['record' => $record])) // Adjust if you have a RequirementResource
                // Or a simpler view URL if no resource exists:
                // ->url(fn (Requirement $record): string => route('some.requirement.view.route', $record))
            ])
            ->emptyStateHeading('No pending requirements found')
            ->description('Showing the latest requirements needing review.');
    }
}
