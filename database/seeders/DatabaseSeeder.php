<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

final class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Disable foreign key checks for SQLite
        DB::statement('PRAGMA foreign_keys = OFF');

        // First create roles and permissions
        $this->call(RoleSeeder::class);

        // Create admin user
        User::factory()->withPersonalTeam()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        // Create additional users for different roles
        User::factory()->create([
            'name' => 'Scholarship Officer',
            'email' => 'officer@example.com',
            'password' => Hash::make('password'),
        ]);

        User::factory()->create([
            'name' => 'Requirement Verifier',
            'email' => 'verifier@example.com',
            'password' => Hash::make('password'),
        ]);

        // Create an admin user
        $admin = \App\Models\User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);
        $admin->assignRole('admin');

        // Run other seeders
        $this->call([
            Schools::class,
            Scholar::class,
            ScholarshipSeeder::class,
            RequirementSeeder::class,
            AnnouncementSeeder::class,
            ScholarScholarshipSeeder::class,
            ScholarshipRequirementSeeder::class,
            // ScholarshipAnnouncementSeeder::class,
        ]);

        // Enable foreign key checks
        DB::statement('PRAGMA foreign_keys = ON');
    }
}
