<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class SystemCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check system for production readiness and issues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Running System Check...');
        $this->newLine();

        $issues = [];
        $warnings = [];
        $checks = [];

        // Check 1: Environment
        $this->line('1. Environment Configuration:');
        if (config('app.env') === 'local') {
            $warnings[] = 'APP_ENV is set to "local" (should be "production" for production)';
            $this->warn('   ⚠ APP_ENV=local');
        } else {
            $this->info('   ✓ APP_ENV=' . config('app.env'));
        }

        if (config('app.debug')) {
            $issues[] = 'APP_DEBUG is enabled (should be false for production)';
            $this->error('   ✗ APP_DEBUG=true');
        } else {
            $this->info('   ✓ APP_DEBUG=false');
        }

        if (empty(config('app.url'))) {
            $warnings[] = 'APP_URL is not set';
            $this->warn('   ⚠ APP_URL not set');
        } else {
            $this->info('   ✓ APP_URL=' . config('app.url'));
        }
        $this->newLine();

        // Check 2: Database
        $this->line('2. Database Connection:');
        try {
            DB::connection()->getPdo();
            $this->info('   ✓ Database connection successful');
            
            // Check indexes
            $tables = ['events', 'markets', 'trades', 'wallets'];
            foreach ($tables as $table) {
                try {
                    $indexes = DB::select("SHOW INDEXES FROM {$table}");
                    if (count($indexes) > 1) { // More than just primary key
                        $this->info("   ✓ Table '{$table}' has indexes");
                    } else {
                        $warnings[] = "Table '{$table}' may need more indexes";
                        $this->warn("   ⚠ Table '{$table}' has minimal indexes");
                    }
                } catch (\Exception $e) {
                    // Table might not exist
                }
            }
        } catch (\Exception $e) {
            $issues[] = 'Database connection failed: ' . $e->getMessage();
            $this->error('   ✗ Database connection failed');
        }
        $this->newLine();

        // Check 3: Cache
        $this->line('3. Cache Configuration:');
        try {
            Cache::put('system_check_test', 'ok', 60);
            if (Cache::get('system_check_test') === 'ok') {
                $this->info('   ✓ Cache is working');
                Cache::forget('system_check_test');
            } else {
                $issues[] = 'Cache is not working properly';
                $this->error('   ✗ Cache test failed');
            }
        } catch (\Exception $e) {
            $issues[] = 'Cache error: ' . $e->getMessage();
            $this->error('   ✗ Cache error');
        }
        $this->newLine();

        // Check 4: Storage
        $this->line('4. Storage & Permissions:');
        $storageWritable = is_writable(storage_path());
        $cacheWritable = is_writable(base_path('bootstrap/cache'));

        if ($storageWritable) {
            $this->info('   ✓ storage/ is writable');
        } else {
            $issues[] = 'storage/ directory is not writable';
            $this->error('   ✗ storage/ is not writable');
        }

        if ($cacheWritable) {
            $this->info('   ✓ bootstrap/cache/ is writable');
        } else {
            $issues[] = 'bootstrap/cache/ directory is not writable';
            $this->error('   ✗ bootstrap/cache/ is not writable');
        }
        $this->newLine();

        // Check 5: Config Cache
        $this->line('5. Configuration Cache:');
        $configCached = File::exists(base_path('bootstrap/cache/config.php'));
        if ($configCached) {
            $this->info('   ✓ Config is cached');
        } else {
            $warnings[] = 'Config cache not found (run: php artisan config:cache)';
            $this->warn('   ⚠ Config not cached');
        }

        $routeCached = File::exists(base_path('bootstrap/cache/routes-v7.php'));
        if ($routeCached) {
            $this->info('   ✓ Routes are cached');
        } else {
            $warnings[] = 'Route cache not found (run: php artisan route:cache)';
            $this->warn('   ⚠ Routes not cached');
        }
        $this->newLine();

        // Check 6: Logging
        $this->line('6. Logging:');
        $logFile = storage_path('logs/laravel.log');
        if (File::exists($logFile)) {
            $logSize = File::size($logFile);
            $logSizeMB = round($logSize / 1024 / 1024, 2);
            $this->info("   ✓ Log file exists ({$logSizeMB} MB)");
            
            if ($logSizeMB > 100) {
                $warnings[] = 'Log file is very large (' . $logSizeMB . ' MB). Consider rotating logs.';
                $this->warn("   ⚠ Log file is large ({$logSizeMB} MB)");
            }
        } else {
            $this->info('   ✓ Log file will be created automatically');
        }
        $this->newLine();

        // Check 7: Security
        $this->line('7. Security:');
        if (empty(config('app.key'))) {
            $issues[] = 'APP_KEY is not set (run: php artisan key:generate)';
            $this->error('   ✗ APP_KEY not set');
        } else {
            $this->info('   ✓ APP_KEY is set');
        }

        if (config('session.encrypt')) {
            $this->info('   ✓ Session encryption enabled');
        } else {
            $warnings[] = 'Session encryption is disabled';
            $this->warn('   ⚠ Session encryption disabled');
        }
        $this->newLine();

        // Summary
        $this->newLine();
        $this->line('==========================================');
        $this->line('Summary:');
        $this->line('==========================================');

        if (empty($issues) && empty($warnings)) {
            $this->info('✅ All checks passed! System is ready for production.');
            return Command::SUCCESS;
        }

        if (!empty($issues)) {
            $this->error('❌ Issues found (' . count($issues) . '):');
            foreach ($issues as $issue) {
                $this->error('   • ' . $issue);
            }
            $this->newLine();
        }

        if (!empty($warnings)) {
            $this->warn('⚠️  Warnings (' . count($warnings) . '):');
            foreach ($warnings as $warning) {
                $this->warn('   • ' . $warning);
            }
            $this->newLine();
        }

        $this->line('Run "php artisan optimize" and fix the issues above for production.');
        
        return empty($issues) ? Command::SUCCESS : Command::FAILURE;
    }
}

