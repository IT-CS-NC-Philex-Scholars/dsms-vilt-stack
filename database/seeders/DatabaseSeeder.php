<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // Import DB facade

final class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::transaction(function () { // Use transaction for atomicity
            $this->call([
                // Core Setup First
                RoleAndPermissionSeeder::class,
                UserSeeder::class,

                // Foundational Data
                Schools::class,       // Uses SchoolFactory (or static)
                ScholarshipSeeder::class, // Uses ScholarshipFactory

                // Dependent Data
                Scholar::class,         // Uses ScholarFactory, needs Schools
                RequirementSeeder::class, // Links Scholars and Scholarships
                AnnouncementSeeder::class,// Uses AnnouncementFactory
            ]);
        });

        $this->command->info('Database seeding completed successfully!');
    }
}
