<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Scholar as ScholarModel; // Keep alias if used elsewhere
use App\Models\School;

class Scholar extends Seeder // Keep original class name
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure schools exist first
        if (School::count() === 0) {
             $this->command->warn('No schools found. Seeding schools first...');
             $this->call(Schools::class); // Make sure Schools seeder exists and is correct
             if (School::count() === 0) {
                 $this->command->error('Failed to seed schools. Cannot create scholars.');
                 return;
             }
        }

        ScholarModel::factory(300)->create(); // Create 300 scholars using the factory
        $this->command->info('Created 300 scholars.');
    }
}
