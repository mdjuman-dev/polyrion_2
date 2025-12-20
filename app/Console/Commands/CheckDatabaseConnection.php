<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckDatabaseConnection extends Command
{
    protected $signature = 'db:check';
    protected $description = 'Check database connection and diagnose issues';

    public function handle()
    {
        $this->info('Checking database connection...');
        $this->newLine();

        // Check .env configuration
        $this->info('📋 Configuration:');
        $this->line('  DB_CONNECTION: ' . config('database.default'));
        $this->line('  DB_HOST: ' . config('database.connections.mysql.host'));
        $this->line('  DB_PORT: ' . config('database.connections.mysql.port'));
        $this->line('  DB_DATABASE: ' . config('database.connections.mysql.database'));
        $this->line('  DB_USERNAME: ' . config('database.connections.mysql.username'));
        $this->newLine();

        // Try connection with current settings
        $this->info('🔍 Testing connection...');
        try {
            DB::connection()->getPdo();
            $this->info('✅ Database connection successful!');
            return Command::SUCCESS;
        } catch (\Illuminate\Database\QueryException $e) {
            $this->error('❌ Database connection failed!');
            $this->newLine();
            
            $errorCode = $e->getCode();
            $errorMessage = $e->getMessage();
            
            $this->error('Error: ' . $errorMessage);
            $this->newLine();
            
            // Provide specific solutions based on error
            if (strpos($errorMessage, 'Connection refused') !== false) {
                $this->warn('💡 Solution:');
                $this->line('  1. Change DB_HOST in .env file:');
                $this->line('     DB_HOST=127.0.0.1  →  DB_HOST=localhost');
                $this->newLine();
                $this->line('  2. Or check if MySQL service is running:');
                $this->line('     sudo systemctl status mysql');
                $this->line('     sudo systemctl start mysql');
                $this->newLine();
                $this->line('  3. After changing .env, run:');
                $this->line('     php artisan config:clear');
                $this->line('     php artisan cache:clear');
            } elseif (strpos($errorMessage, 'Access denied') !== false) {
                $this->warn('💡 Solution:');
                $this->line('  Check DB_USERNAME and DB_PASSWORD in .env file');
            } elseif (strpos($errorMessage, 'Unknown database') !== false) {
                $this->warn('💡 Solution:');
                $this->line('  Database does not exist. Create it or update DB_DATABASE in .env');
            } else {
                $this->warn('💡 General Solutions:');
                $this->line('  1. Check MySQL service: sudo systemctl status mysql');
                $this->line('  2. Verify .env file settings');
                $this->line('  3. Check MySQL socket: mysql_config --socket');
                $this->line('  4. Try: DB_HOST=localhost instead of 127.0.0.1');
            }
            
            return Command::FAILURE;
        } catch (\Exception $e) {
            $this->error('❌ Unexpected error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}

