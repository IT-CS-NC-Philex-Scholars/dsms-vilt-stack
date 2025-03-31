<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        $admin = User::factory()->withPersonalTeam()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com', // Standard admin email
            'password' => Hash::make('password'), // Use a strong password in production!
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('Admin'); // Assign Admin role
        $this->command->info('Admin user created: admin@example.com / password');


        // Create Regular Users
        User::factory(10)->withPersonalTeam()->create()->each(function ($user) {
            $user->assignRole('User'); // Assign basic user role
        });
         $this->command->info('Created 10 regular users.');
    }
}
