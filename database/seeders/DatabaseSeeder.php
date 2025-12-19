<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\AdminSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Only seed roles/permissions if Spatie Permission is installed
        if (class_exists(\Spatie\Permission\Models\Role::class)) {
            $this->call([
                RolePermissionSeeder::class,
            ]);
        } else {
            $this->command->warn('Spatie Permission package not installed. Skipping role/permission seeding.');
            $this->command->info('To enable roles/permissions, run: composer require spatie/laravel-permission');
        }
        
        // Always seed admin user
        $this->call([
            AdminSeeder::class,
        ]);
    }
}
