<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Create roles
        $admin = Role::create(['name' => 'admin']);
        $scholar = Role::create(['name' => 'scholar']);

        // Create permissions
        $permissions = [
            'view applications',
            'review applications',
            'manage scholars',
            'upload documents',
            'submit applications',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Assign permissions to roles
        $admin->givePermissionTo([
            'view applications',
            'review applications',
            'manage scholars',
        ]);

        $scholar->givePermissionTo([
            'upload documents',
            'submit applications',
        ]);
    }
}
