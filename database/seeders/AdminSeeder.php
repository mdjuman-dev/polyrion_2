<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin
        $superAdmin = Admin::firstOrCreate(
            ['email' => 'superadmin@admin.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('superadmin123'),
            ]
        );

        // Create regular admin
        $admin = Admin::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
            ]
        );

        // Assign roles and permissions if Spatie Permission is installed
        if (class_exists(\Spatie\Permission\Models\Role::class)) {
            try {
                // Reset cached permissions
                if (class_exists(\Spatie\Permission\PermissionRegistrar::class)) {
                    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
                }

                // Get or create admin role
                $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(
                    ['name' => 'admin', 'guard_name' => 'admin']
                );

                // Get all permissions
                $allPermissions = \Spatie\Permission\Models\Permission::where('guard_name', 'admin')->get();
                
                // Assign all permissions to admin role
                if ($allPermissions->count() > 0) {
                    $adminRole->syncPermissions($allPermissions);
                }

                // Assign admin role to super admin
                if (method_exists($superAdmin, 'assignRole')) {
                    $superAdmin->assignRole($adminRole);
                    $this->command->info('Super Admin created: superadmin@admin.com / superadmin123');
                }

                // Assign admin role to regular admin
                if (method_exists($admin, 'assignRole')) {
                    $admin->assignRole($adminRole);
                    $this->command->info('Admin created: admin@gmail.com / password');
                }

            } catch (\Exception $e) {
                $this->command->warn('Could not assign roles/permissions: ' . $e->getMessage());
                $this->command->info('Make sure to run RolePermissionSeeder first: php artisan db:seed --class=RolePermissionSeeder');
            }
        } else {
            $this->command->info('Super Admin created: superadmin@admin.com / superadmin123');
            $this->command->info('Admin created: admin@gmail.com / password');
            $this->command->warn('Spatie Permission not installed. Roles/permissions not assigned.');
        }
    }
}
