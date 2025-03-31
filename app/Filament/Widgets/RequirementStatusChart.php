<?php

namespace App\Filament\Widgets;

use App\Models\Requirement;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RequirementStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Overall Requirement Status';
    protected static ?int $sort = 7;
    protected static string $color = 'danger';

    protected function getData(): array
    {
        $data = Requirement::query()
            // Optionally exclude 'missing' if you only want submitted statuses
            // ->where('status', '!=', 'missing')
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        // Define colors based on likely status meaning
        $statusColors = [
            'approved' => '#10B981', // Emerald 500
            'submitted' => '#3B82F6', // Blue 500
            'pending' => '#F59E0B',   // Amber 500 (if you use 'pending')
            'rejected' => '#EF4444', // Red 500
            'pending_resubmission' => '#F97316', // Orange 500
            'missing' => '#6B7280', // Gray 500
            // Add other statuses and their colors
        ];

        $backgroundColors = $data->keys()->map(fn($status) => $statusColors[$status] ?? '#9CA3AF')->toArray(); // Default to gray

        return [
            'datasets' => [
                [
                    'label' => 'Requirements',
                    'data' => $data->values()->toArray(),
                    'backgroundColor' => $backgroundColors,
                    'borderColor' => '#ffffff',
                ],
            ],
            'labels' => $data->keys()->map(fn ($status) => Str::of($status)->replace('_', ' ')->title())->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
