<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\School;
use Illuminate\Database\Seeder; // Keep alias if used elsewhere
use App\Models\Scholar as ScholarModel;

final class Scholar extends Seeder // Keep original class name
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure schools exist first
        if (\App\Models\School::query()->count() === 0) {
            $this->command->warn('No schools found. Seeding schools first...');
            $this->call(Schools::class); // Make sure Schools seeder exists and is correct
            if (\App\Models\School::query()->count() === 0) {
                $this->command->error('Failed to seed schools. Cannot create scholars.');

                return;
            }
        }

        ScholarModel::factory(300)->create(); // Create 300 scholars using the factory
        $this->command->info('Created 300 scholars.');
    }
}
