<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'view dashboard',
            'manage events',
            'create events',
            'edit events',
            'delete events',
            'manage users',
            'view users',
            'edit users',
            'delete users',
            'manage withdrawals',
            'approve withdrawals',
            'reject withdrawals',
            'manage settings',
            'manage global settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'admin'
            ]);
        }

        // Create admin role and assign all permissions
        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'admin'
        ]);

        $adminRole->givePermissionTo(Permission::where('guard_name', 'admin')->get());
    }
}
