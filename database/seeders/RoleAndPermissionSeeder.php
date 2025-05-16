<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

final class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Define Permissions (Adjust based on your Filament Resources/Policies)
        $permissions = [
            'view_user', 'create_user', 'edit_user', 'delete_user',
            'view_role', 'create_role', 'edit_role', 'delete_role',
            'view_school', 'create_school', 'edit_school', 'delete_school',
            'view_scholar', 'create_scholar', 'edit_scholar', 'delete_scholar',
            'view_scholarship', 'create_scholarship', 'edit_scholarship', 'delete_scholarship',
            'view_requirement', 'create_requirement', 'edit_requirement', 'delete_requirement', 'review_requirement',
            'view_announcement', 'create_announcement', 'edit_announcement', 'delete_announcement',
            'view_shield', // For Filament Shield plugin access
            'view_dashboard', // General dashboard access
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        // Create Roles
        $adminRole = Role::findOrCreate('Admin', 'web');
        $userRole = Role::findOrCreate('User', 'web'); // Example: Basic user/scholar role

        // Assign Permissions to Roles
        // Admin gets all permissions
        $adminRole->givePermissionTo(Permission::all());

        // User role gets basic view permissions (adjust as needed)
        $userRole->givePermissionTo([
            'view_dashboard',
            'view_school',
            'view_scholarship',
            'view_announcement',
            // Maybe allow scholars to view/edit their own requirements? Needs policy implementation.
            // 'view_requirement',
            // 'create_requirement', // If they submit through the system
        ]);

        $this->command->info('Roles and Permissions seeded successfully.');
    }
}
