<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ScholarResource; // Assuming you have this resource
use App\Models\Scholar;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Str;

class RecentScholarsTable extends BaseWidget
{
    protected static ?int $sort = 8;
    protected int | string | array $columnSpan = 'full'; // Make it take full width

    protected static ?string $heading = 'Recently Added Scholars';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Scholar::query()
                    ->with('school') // Eager load school
                    ->latest('created_at') // Order by creation date
                    ->limit(10) // Show latest 10
            )
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Name')
                    ->searchable(['first_name', 'last_name']), // Search parts of name
                Tables\Columns\TextColumn::make('school.name')
                    ->label('School')
                    ->limit(30)
                    ->tooltip(fn (Scholar $record): string => $record->school?->name ?? '')
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                     ->formatStateUsing(fn (string $state): string => Str::of($state)->replace('_', ' ')->title())
                     ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date Added')
                    ->dateTime()
                    ->sortable()
                    ->since(), // Show relative time
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('View Profile')
                    ->icon('heroicon-m-user')
                    // Link to your ScholarResource edit/view page
                    ->url(fn (Scholar $record): string => ScholarResource::getUrl('edit', ['record' => $record]))
                    ->openUrlInNewTab(),
            ])
             ->emptyStateHeading('No scholars added recently.');
    }
}
