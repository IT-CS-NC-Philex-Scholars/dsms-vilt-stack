<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Scholar;
use Illuminate\Support\Str;
use Filament\Widgets\ChartWidget; // For type hint
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

final class ScholarTypeDistributionChart extends ChartWidget
{
    protected static ?string $heading = 'Scholar Distribution by Type';

    protected static ?int $sort = 3; // Keep or adjust

    // Consider changing color theme if desired e.g. 'success', 'warning'
    protected static string $color = 'primary';

    // Optional: Add a description for context
    public function getDescription(): ?string
    {
        return 'Shows the proportion of scholars belonging to each designated type.';
    }

    protected function getData(): array
    {
        /** @var Collection $data */ // Add type hint for better IDE support
        $data = Scholar::query()
            ->select('type', DB::raw('count(*) as count'))
            ->whereNotNull('type') // Ensure we don't count scholars with null type
            ->groupBy('type')
            ->pluck('count', 'type'); // Keys are types, values are counts

        // If no data, return an empty structure to avoid errors
        if ($data->isEmpty()) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        // --- Dynamic Color Mapping ---
        // Define specific colors for known, common types for consistency
        $typeColors = [
            'College' => '#36A2EB',     // Blue
            'High_School' => '#FFCE56', // Yellow
            // Add other common types you expect and assign specific colors:
            // 'Graduate' => '#4BC0C0',    // Teal
            // 'Vocational' => '#9966FF', // Purple
        ];
        // Define a fallback color for any types not explicitly listed above
        $defaultColor = '#A0AEC0'; // Example: Gray 500

        $labels = $data->keys();
        $counts = $data->values();

        // Generate the backgroundColor array dynamically based on the actual labels found
        $backgroundColors = $labels->map(function ($type) use ($typeColors, $defaultColor): string {
            return $typeColors[$type] ?? $defaultColor; // Use specific color if defined, else default
        });

        return [
            'datasets' => [
                [
                    // Use a simpler label, the legend will show type names
                    'label' => 'Scholars',
                    'data' => $counts->toArray(),
                    'backgroundColor' => $backgroundColors->toArray(),
                    'borderColor' => '#ffffff', // White border looks good on pie/doughnut
                ],
            ],
            // Format labels clearly (e.g., High_School -> High School)
            'labels' => $labels->map(fn ($type) => Str::title(str_replace('_', ' ', $type)))->toArray(),
        ];
    }

    protected function getType(): string
    {
        // Doughnut is often preferred visually over pie
        return 'doughnut';
    }
}
