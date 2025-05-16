<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use Carbon\Carbon;
use App\Models\User;
use App\Models\School;
use App\Models\Scholar;
use App\Models\Requirement;
use App\Models\Scholarship;
use App\Models\Announcement; // Import Carbon for date calculations
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

final class DashboardStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1; // Keep it first

    // Optional: Make widgets refresh data periodically
    // protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        // --- Date Ranges for Comparisons ---
        $startDate7Days = now()->subDays(7);
        $startDate30Days = now()->subDays(30);
        $endDate = now();

        // --- Calculations ---

        // Users
        $totalUsers = \App\Models\User::query()->count();
        $newUsersLast7Days = \App\Models\User::query()->where('created_at', '>=', $startDate7Days)->count();

        // Scholars
        $totalScholars = \App\Models\Scholar::query()->count();
        $activeScholars = \App\Models\Scholar::query()->where('status', 'active')->count();
        $newScholarsLast30Days = \App\Models\Scholar::query()->where('created_at', '>=', $startDate30Days)->count();
        $graduatedScholarsLast30Days = \App\Models\Scholar::query()->where('status', 'graduated')
                                            // Assuming you have an updated_at or specific graduation_date field
            ->where('updated_at', '>=', $startDate30Days)
            ->count();

        // Scholarships
        $totalScholarships = \App\Models\Scholarship::query()->count();
        $activeScholarships = \App\Models\Scholarship::query()->where('status', 'active')->count();
        $scholarshipsEndingSoon = \App\Models\Scholarship::query()->where('status', 'active')
            ->whereNotNull('application_deadline')
            ->whereBetween('application_deadline', [$endDate, $endDate->copy()->addDays(30)]) // Deadline within next 30 days
            ->count();

        // Requirements
        $pendingStatuses = ['pending', 'submitted']; // Adjust as needed
        $pendingRequirements = \App\Models\Requirement::query()->whereIn('status', $pendingStatuses)->count();
        $approvedRequirementsLast7Days = \App\Models\Requirement::query()->where('status', 'approved')
            ->where('reviewed_at', '>=', $startDate7Days) // Assuming reviewed_at is set on approval
            ->count();
        // Trend for pending requirements (last 7 days count per day)
        $pendingTrend = Requirement::query()
            ->whereIn('status', $pendingStatuses)
            ->where('submitted_at', '>=', $startDate7Days) // Consider requirements submitted recently
            ->selectRaw('DATE(submitted_at) as date, count(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count')
            ->toArray();

        // Other Counts
        $totalSchools = \App\Models\School::query()->where('is_active', true)->count(); // Count only active schools
        \App\Models\Announcement::query()->where('published_at', '<=', $endDate)
                                            // Optional: Add an expiry date check if you have one
                                            // ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', $endDate))
            ->count();

        // --- Build Stats ---
        return [
            Stat::make('Total Scholars', $totalScholars)
                ->description($activeScholars.' Active')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('success')
            // Add a small chart showing new scholars trend if desired (more complex query needed)
            // ->chart([/* daily new scholar counts for last 7 days */])
            ,

            Stat::make('New Scholars (Last 30d)', $newScholarsLast30Days)
                ->description($graduatedScholarsLast30Days.' Graduated/Left in period')
                ->descriptionIcon('heroicon-m-user-plus', 'before') // Icon before text
                ->color('info'),

            Stat::make('Pending Requirements', $pendingRequirements)
                ->description('Awaiting Review / Action')
                ->descriptionIcon($pendingRequirements > 50 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-document-arrow-up') // Change icon if count is high
                ->color($pendingRequirements > 0 ? 'warning' : 'gray')
                ->chart($pendingTrend) // Show the trend of pending submissions
            ,

            Stat::make('Approved Requirements (Last 7d)', $approvedRequirementsLast7Days)
                ->description('Reviews Completed Recently')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            Stat::make('Active Scholarships', $totalScholarships) // Show total, describe active
                ->description($activeScholarships.' Active Programs')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('primary'),

            Stat::make('Deadlines Approaching (30d)', $scholarshipsEndingSoon)
                ->description('Scholarships closing soon')
                ->descriptionIcon('heroicon-m-clock')
                ->color($scholarshipsEndingSoon > 0 ? 'danger' : 'gray'), // Danger if deadlines are near

            Stat::make('Total Users', $totalUsers)
                ->description($newUsersLast7Days.' New users this week')
                ->descriptionIcon('heroicon-m-users')
                ->color('gray'), // Less prominent color

            Stat::make('Active Schools', $totalSchools)
                ->description('Partner Institutions')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('gray'), // Less prominent color

            // Keep announcements simple unless critical
            // Stat::make('Active Announcements', $activeAnnouncements)
            //    ->descriptionIcon('heroicon-m-megaphone')
            //    ->color('info'),

        ];
    }

    // Optional helper for percentage change (if needed, more complex)
    private function calculatePercentageChange(int $current, int $previous): ?float
    {
        if ($previous === 0) {
            return $current > 0 ? 100.0 : 0.0; // Avoid division by zero, show 100% increase if starting from 0
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }
}
