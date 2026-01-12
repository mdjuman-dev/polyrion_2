<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if Spatie Permission package is installed
        if (!class_exists(\Spatie\Permission\Models\Role::class) || 
            !class_exists(\Spatie\Permission\Models\Permission::class)) {
            // Package not installed, skip seeding
            $this->command->warn('Spatie Permission package not installed. Skipping role/permission seeding.');
            $this->command->info('To install: composer require spatie/laravel-permission');
            return;
        }

        try {
            // Reset cached roles and permissions
            if (class_exists(\Spatie\Permission\PermissionRegistrar::class)) {
                app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
            }

            $permissions = [
                // Dashboard
                'view dashboard',
                
                // Events
                'manage events',
                'create events',
                'edit events',
                'delete events',
                'view events',
                
                // Markets
                'manage markets',
                'create markets',
                'edit markets',
                'delete markets',
                'view markets',
                'settle markets',
                
                // Users
                'manage users',
                'view users',
                'edit users',
                'delete users',
                
                // Deposits
                'manage deposits',
                'view deposits',
                'approve deposits',
                'reject deposits',
                
                // Withdrawals
                'manage withdrawals',
                'view withdrawals',
                'approve withdrawals',
                'reject withdrawals',
                'process withdrawals',
                
                // Settings
                'manage settings',
                'manage global settings',
                'manage payment settings',
                'manage pages',
                'manage faqs',
                'manage contact',
                'manage social media',
                
                // Roles & Permissions
                'manage roles',
                'view roles',
                'create roles',
                'edit roles',
                'delete roles',
                'manage permissions',
                'view permissions',
                'create permissions',
                'delete permissions',
            ];

            foreach ($permissions as $permission) {
                \Spatie\Permission\Models\Permission::firstOrCreate([
                    'name' => $permission,
                    'guard_name' => 'admin'
                ]);
            }

            // Create admin role and assign all permissions
            $adminRole = \Spatie\Permission\Models\Role::firstOrCreate([
                'name' => 'admin',
                'guard_name' => 'admin'
            ]);

            $adminRole->givePermissionTo(\Spatie\Permission\Models\Permission::where('guard_name', 'admin')->get());
            
            $this->command->info('Roles and permissions seeded successfully.');
        } catch (\Exception $e) {
            // If tables don't exist yet, that's okay - migrations will create them
            $this->command->warn('Could not seed roles/permissions: ' . $e->getMessage());
            $this->command->info('Make sure to run migrations first: php artisan migrate');
        }
    }
}
