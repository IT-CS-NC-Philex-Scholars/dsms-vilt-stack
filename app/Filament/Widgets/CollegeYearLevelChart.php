<?php

namespace App\Filament\Widgets;

use App\Models\Scholar;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class CollegeYearLevelChart extends ChartWidget
{
    protected static ?string $heading = 'College Scholars by Year Level';
    protected static ?int $sort = 6;
    protected static string $color = 'warning';

    protected function getData(): array
    {
        $data = Scholar::query()
            ->where('type', 'College') // Filter for College students only
            ->select('year_level', DB::raw('count(*) as count'))
            ->groupBy('year_level')
            ->orderBy('year_level') // Ensure order is 1, 2, 3, 4...
            ->pluck('count', 'year_level');

        return [
            'datasets' => [
                [
                    'label' => 'College Scholars',
                    'data' => $data->values()->toArray(),
                    'backgroundColor' => '#FBBF24', // Amber 400
                    'borderColor' => '#FBBF24',
                ],
            ],
            'labels' => $data->keys()->map(fn ($year) => 'Year ' . $year)->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    // Optional: Add a filter description
    // protected function getFilters(): ?array
    // {
    //     return [
    //         'filter' => 'Showing data for College scholars only.',
    //     ];
    // }
}
