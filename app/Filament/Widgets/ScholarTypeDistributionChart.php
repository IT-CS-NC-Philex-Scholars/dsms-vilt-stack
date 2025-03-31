<?php

namespace App\Filament\Widgets;
use App\Models\Scholar;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str; // Import Str

class ScholarTypeDistributionChart extends ChartWidget
{
    protected static ?string $heading = 'Scholar Distribution by Type';
    protected static ?int $sort = 5; // Adjust sort order as needed
    protected static string $color = 'primary';

    protected function getData(): array
    {
        $data = Scholar::query()
            ->select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->pluck('count', 'type');

        return [
            'datasets' => [
                [
                    'label' => 'Scholars by Type',
                    'data' => $data->values()->toArray(),
                    // Define distinct colors
                    'backgroundColor' => [
                        '#FF6384', // Example Pink
                        '#36A2EB', // Example Blue
                        '#FFCE56', // Example Yellow
                        '#4BC0C0', // Example Teal
                        '#9966FF', // Example Purple
                        '#FF9F40', // Example Orange
                    ],
                    'borderColor' => '#ffffff',
                ],
            ],
            // Format labels nicely (e.g., High_School -> High School)
            'labels' => $data->keys()->map(fn ($type) => Str::of($type)->replace('_', ' ')->title())->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
