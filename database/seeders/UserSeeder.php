<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Team;

final class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'], // Attributes to find the user
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'), // Use a strong password in production!
                'email_verified_at' => now(),
            ]
        );

        // Ensure the admin user has a personal team if it was newly created or doesn't have one
        if ($admin->wasRecentlyCreated || is_null($admin->current_team_id)) {
            $admin->ownedTeams()->save(Team::forceCreate([
                'user_id' => $admin->id,
                'name' => explode(' ', $admin->name, 2)[0]."'s Team",
                'personal_team' => true,
            ]));
            // Switch to the newly created personal team
            $admin->switchTeam($admin->personalTeam());
        }

        if (!$admin->hasRole('Admin')) {
            $admin->assignRole('Admin'); // Assign Admin role if not already assigned
        }

        if ($admin->wasRecentlyCreated) {
            $this->command->info('Admin user created: admin@example.com / password');
        } else {
            $this->command->info('Admin user admin@example.com already exists.');
        }

        // Create Regular Users
        // Check if users exist before creating them to avoid issues if run multiple times.
        if (User::where('email', '!=', 'admin@example.com')->count() < 10) {
            User::factory(10)->withPersonalTeam()->create()->each(function ($user): void {
                if (!$user->hasRole('User')) {
                    $user->assignRole('User'); // Assign basic user role
                }
            });
            $this->command->info('Created additional regular users.');
        } else {
            $this->command->info('Sufficient regular users already exist.');
        }
    }
}
