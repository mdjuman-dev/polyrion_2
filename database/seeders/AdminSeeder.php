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
        $admin = Admin::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
            ]
        );

        // Assign admin role if Spatie Permission is installed and role exists
        if (class_exists(\Spatie\Permission\Models\Role::class)) {
            try {
                $adminRole = \Spatie\Permission\Models\Role::where('name', 'admin')
                    ->where('guard_name', 'admin')
                    ->first();
                
                if ($adminRole && method_exists($admin, 'assignRole')) {
                    $admin->assignRole($adminRole);
                }
            } catch (\Exception $e) {
                // Silently fail if roles/permissions tables don't exist yet
                // This is okay - migrations will create them
            }
        }
    }
}
