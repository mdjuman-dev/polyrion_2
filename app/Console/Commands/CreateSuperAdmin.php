<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateSuperAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create-super-admin 
                            {--name=Super Admin : Admin name}
                            {--email=superadmin@admin.com : Admin email}
                            {--password= : Admin password (if not provided, will be generated)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a super admin user with all permissions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->option('name');
        $email = $this->option('email');
        $password = $this->option('password');

        // Validate email
        $validator = Validator::make(['email' => $email], [
            'email' => 'required|email|unique:admins,email'
        ]);

        if ($validator->fails()) {
            $this->error('Email validation failed: ' . $validator->errors()->first('email'));
            
            if ($this->confirm('Do you want to update the existing admin instead?', true)) {
                $admin = Admin::where('email', $email)->first();
                if ($admin) {
                    return $this->updateAdmin($admin, $name, $password);
                }
            }
            
            return 1;
        }

        // Generate password if not provided
        if (!$password) {
            $password = $this->generatePassword();
            $this->info('Generated password: ' . $password);
        }

        // Validate password
        if (strlen($password) < 8) {
            $this->error('Password must be at least 8 characters long.');
            return 1;
        }

        try {
            // Create super admin
            $admin = Admin::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
            ]);

            // Assign admin role and all permissions
            if (class_exists(\Spatie\Permission\Models\Role::class)) {
                // Reset cached permissions
                if (class_exists(\Spatie\Permission\PermissionRegistrar::class)) {
                    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
                }

                // Get or create admin role
                $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(
                    ['name' => 'admin', 'guard_name' => 'admin']
                );

                // Get all permissions and assign to role
                $allPermissions = \Spatie\Permission\Models\Permission::where('guard_name', 'admin')->get();
                if ($allPermissions->count() > 0) {
                    $adminRole->syncPermissions($allPermissions);
                }

                // Assign role to admin
                $admin->assignRole($adminRole);

                $this->info('✅ Super Admin created successfully!');
                $this->table(
                    ['Field', 'Value'],
                    [
                        ['Name', $admin->name],
                        ['Email', $admin->email],
                        ['Password', $password],
                        ['Role', 'admin'],
                        ['Permissions', $allPermissions->count() . ' permissions assigned'],
                    ]
                );

                $this->warn('⚠️  Please save these credentials securely!');
            } else {
                $this->warn('⚠️  Spatie Permission package not installed. Admin created without roles/permissions.');
                $this->info('✅ Admin created successfully!');
                $this->table(
                    ['Field', 'Value'],
                    [
                        ['Name', $admin->name],
                        ['Email', $admin->email],
                        ['Password', $password],
                    ]
                );
            }

            return 0;
        } catch (\Exception $e) {
            $this->error('Failed to create super admin: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Update existing admin
     */
    private function updateAdmin($admin, $name, $password)
    {
        $admin->name = $name;
        
        if ($password) {
            if (strlen($password) < 8) {
                $this->error('Password must be at least 8 characters long.');
                return 1;
            }
            $admin->password = Hash::make($password);
        }
        
        $admin->save();

        // Assign admin role if not already assigned
        if (class_exists(\Spatie\Permission\Models\Role::class)) {
            if (class_exists(\Spatie\Permission\PermissionRegistrar::class)) {
                app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
            }

            $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(
                ['name' => 'admin', 'guard_name' => 'admin']
            );

            $allPermissions = \Spatie\Permission\Models\Permission::where('guard_name', 'admin')->get();
            if ($allPermissions->count() > 0) {
                $adminRole->syncPermissions($allPermissions);
            }

            if (!$admin->hasRole($adminRole)) {
                $admin->assignRole($adminRole);
            }
        }

        $this->info('✅ Admin updated successfully!');
        $this->table(
            ['Field', 'Value'],
            [
                ['Name', $admin->name],
                ['Email', $admin->email],
                ['Password', $password ? 'Updated' : 'Unchanged'],
            ]
        );

        return 0;
    }

    /**
     * Generate a random password
     */
    private function generatePassword($length = 12)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $password;
    }
}
