<?php

use App\Filament\Resources\AnnouncementResource; // If you have one
use App\Models\Announcement;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentAnnouncementsTable extends BaseWidget
{
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Announcement::query()
                    ->whereNotNull('published_at')
                    ->where('published_at', '<=', now())
                    ->latest('published_at')
                    ->limit(5)
            )
            ->heading('Recent Announcements')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(50), // Truncate long titles
                Tables\Columns\TextColumn::make('priority')
                     ->badge()
                     ->colors([
                         'danger' => 'high', // Assuming priority values like 'high', 'medium', 'low'
                         'warning' => 'medium',
                         'success' => 'low',
                         'gray' => fn ($state) => !in_array($state, ['high', 'medium', 'low']),
                     ])
                     ->formatStateUsing(fn ($state): string => ucfirst($state)),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Published On')
                    ->date()
                    ->sortable(),
            ])
            ->actions([
                 Tables\Actions\Action::make('view')
                    ->label('View')
                    ->icon('heroicon-m-eye')
                    // ->url(fn (Announcement $record): string => AnnouncementResource::getUrl('view', ['record' => $record])) // Adjust if you have an AnnouncementResource
            ])
            ->emptyStateHeading('No recent announcements');
    }
}
