<?php

namespace App\Filament\Widgets;

use App\Models\Scholar;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters; // <-- Import this trait
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ScholarYearLevelDistributionChart extends ChartWidget
{
    // Use trait to access page filters if you have them,
    // otherwise, we'll use the widget's internal filter.
    // use InteractsWithPageFilters; // Uncomment if using page-level filters

    protected static ?string $heading = 'Scholar Distribution by Year Level';
    protected static ?int $sort = 2; // Keep or adjust sort order
    protected static string $color = 'info'; // Change color if desired

    // Property to hold the filter state
    public ?string $filter = 'all';

    protected function getFilters(): ?array
    {
        // Define the filter options
        return [
            'all' => 'All Scholars',
            'College' => 'College Only',
            'High_School' => 'High School Only', // Use the exact value stored in your 'type' column
        ];
    }

    protected function getData(): array
    {
        // Base query - select type, year_level, and count
        $query = Scholar::query()
            ->select('type', 'year_level', DB::raw('count(*) as count'))
            ->whereNotNull('year_level') // Ignore scholars without a year level
            ->whereIn('type', ['College', 'High_School']); // Only include these types

        // Apply the filter based on the $this->filter property
        if ($this->filter && $this->filter !== 'all') {
            $query->where('type', $this->filter);
        }

        $data = $query->groupBy('type', 'year_level')
                      ->orderBy('year_level') // Order primarily by year level
                      ->orderBy('type')       // Then by type for consistency
                      ->get();

        // --- Data Processing for Stacked Bar Chart ---

        // 1. Get all unique year levels present in the filtered data
        $yearLevels = $data->pluck('year_level')->unique()->sort()->values();

        // 2. Prepare datasets for each type (College, High School)
        $datasets = [];
        $types = $data->pluck('type')->unique()->sort()->values(); // Get relevant types ('College', 'High_School')

        // Define colors for each type
        $backgroundColors = [
            'College' => '#36A2EB', // Blue
            'High_School' => '#FFCE56', // Yellow
            // Add more if needed
        ];
        $borderColors = [
            'College' => '#36A2EB',
            'High_School' => '#FFCE56',
            // Add more if needed
        ];


        foreach ($types as $type) {
            $typeData = [];
            // For each unique year level, find the count for the current type
            foreach ($yearLevels as $level) {
                $count = $data->firstWhere(fn($item) => $item->type === $type && $item->year_level == $level)?->count ?? 0;
                $typeData[] = $count;
            }

            $datasets[] = [
                // Format type name nicely for the label
                'label' => Str::of($type)->replace('_', ' ')->title() . ' Scholars',
                'data' => $typeData,
                'backgroundColor' => $backgroundColors[$type] ?? '#CCCCCC', // Default grey
                'borderColor' => $borderColors[$type] ?? '#CCCCCC',
                'stack' => 'scholars', // <-- Key for stacking bars
            ];
        }

        // If no data after filtering, provide empty structure
        if ($data->isEmpty()) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }


        // 3. Create labels (e.g., "Year 1", "Grade 7")
        $labels = $yearLevels->map(function ($level) use ($data) {
            // Attempt to determine if it's College year or High School grade
            // This is a simple heuristic, adjust if your levels overlap or are different
            $predominantType = $data->where('year_level', $level)
                                   ->sortByDesc('count')
                                   ->first()?->type ?? 'Unknown';

            if ($predominantType === 'College') {
                 // Check if level is numeric before prefixing 'Year '
                 return is_numeric($level) ? 'Year ' . $level : $level;
            } elseif ($predominantType === 'High_School') {
                 // Check if level is numeric before prefixing 'Grade '
                 return is_numeric($level) ? 'Grade ' . $level : $level;
            } else {
                 // Fallback if type is unknown or level isn't clearly C/HS
                 return 'Level ' . $level;
            }
        })->toArray();


        return [
            'datasets' => $datasets,
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        // Use 'bar' type. Stacked configuration happens in the dataset options.
        return 'bar';
    }

    // Optional: Make heading dynamic based on filter
    public function getHeading(): string
    {
        $filterText = match ($this->filter) {
            'College' => ' (College Only)',
            'High_School' => ' (High School Only)',
            default => '', // 'all' or null
        };
        return 'Scholar Distribution by Year Level' . $filterText;
    }

     // Optional: Add description
     public function getDescription(): ?string
    {
        return 'Shows the number of scholars per year level, grouped by type.';
    }
}
