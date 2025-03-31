<?php

namespace App\Filament\Widgets;
use App\Models\Scholarship;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ScholarshipsByStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Scholarships by Status';
       protected static ?int $sort = 3; // Controls order on dashboard
       protected static string $color = 'info'; // Chart main color theme

       protected function getData(): array
       {
            $data = Scholarship::select('status', DB::raw('count(*) as count'))
               ->groupBy('status')
               ->pluck('count', 'status');

           return [
               'datasets' => [
                   [
                       'label' => 'Scholarships',
                       'data' => $data->values()->toArray(),
                       'backgroundColor' => [
                            '#34D399', // Emerald 500 (e.g., Active)
                            '#FBBF24', // Amber 400 (e.g., Upcoming/Closed)
                            '#6B7280', // Gray 500 (e.g., Archived)
                            // Add more colors if needed
                       ],
                        'borderColor' => '#ffffff',
                   ],
               ],
               'labels' => $data->keys()->map(fn ($status) => ucfirst($status))->toArray(),
           ];
       }

       protected function getType(): string
       {
           return 'doughnut'; // or 'pie'
       }
}
