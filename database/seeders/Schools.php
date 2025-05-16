<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\School;
use Illuminate\Database\Seeder;

final class Schools extends Seeder // Keep original class name if referenced elsewhere
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Option 1: Create specific schools if needed (like your original)
        $staticSchools = [
            [
                'name' => 'Philippine Science High School - Main Campus',
                'address' => 'Agham Rd, Diliman',
                'city' => 'Quezon City', 'province' => 'Metro Manila', 'region' => 'NCR',
                'type' => 'shs',
                'level' => 'High School', 'is_active' => true,
                'email' => 'pisaymc@pshs.edu.ph', 'website' => 'pshs.edu.ph',
                'additional_info' => ['founded' => 1964, 'original_type' => 'Public'],
            ],
            [
                'name' => 'University of the Philippines Diliman',
                'address' => 'Diliman',
                'city' => 'Quezon City', 'province' => 'Metro Manila', 'region' => 'NCR',
                'type' => 'college',
                'level' => 'University', 'is_active' => true,
                'email' => 'info@upd.edu.ph', 'website' => 'upd.edu.ph',
                'additional_info' => ['founded' => 1908, 'student_population' => 25000, 'original_type' => 'SUC'],
            ],
            [
                'name' => 'Ateneo de Manila University',
                'address' => 'Katipunan Ave, Loyola Heights',
                'city' => 'Quezon City', 'province' => 'Metro Manila', 'region' => 'NCR',
                'type' => 'college',
                'level' => 'University', 'is_active' => true,
                'email' => 'info@ateneo.edu', 'website' => 'ateneo.edu',
                'additional_info' => ['founded' => 1859, 'accreditation_level' => 'Level IV', 'original_type' => 'Private'],
            ],
            // Add more specific schools if desired
        ];

        foreach ($staticSchools as $schoolData) {
            \App\Models\School::query()->firstOrCreate(['email' => $schoolData['email']], $schoolData); // Avoid duplicates based on email
        }

        $this->command->info('Created/updated specific schools with type adjusted for form compatibility.');

        // // Option 2: Create additional random schools using the factory
        // $needed = 20 - School::count(); // Create up to 20 total schools
        // if ($needed > 0) {
        //     School::factory($needed)->create();
        //     $this->command->info("Created $needed additional random schools.");
        // }
    }
}
