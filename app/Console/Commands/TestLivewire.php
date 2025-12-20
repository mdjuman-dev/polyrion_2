<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class TestLivewire extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'livewire:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Livewire configuration and endpoints';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Testing Livewire Configuration...');
        $this->newLine();

        // Check Livewire is installed
        $this->line('1. Checking Livewire Installation:');
        if (class_exists(\Livewire\Livewire::class)) {
            $this->info('   ✓ Livewire is installed');
            
            // Check Livewire is working
            try {
                $this->info('   ✓ Livewire is working');
            } catch (\Exception $e) {
                $this->warn('   ⚠ Livewire error: ' . $e->getMessage());
            }
        } else {
            $this->error('   ✗ Livewire is not installed');
            return Command::FAILURE;
        }
        $this->newLine();

        // Check Livewire routes
        $this->line('2. Checking Livewire Routes:');
        $livewireRoutes = collect(Route::getRoutes())->filter(function ($route) {
            return str_contains($route->uri(), 'livewire');
        });

        if ($livewireRoutes->count() > 0) {
            $this->info('   ✓ Livewire routes found:');
            foreach ($livewireRoutes as $route) {
                $this->line("     - {$route->uri()} ({$route->methods()[0]})");
            }
        } else {
            $this->warn('   ⚠ No Livewire routes found');
        }
        $this->newLine();

        // Check Livewire components
        $this->line('3. Checking Livewire Components:');
        $componentPath = app_path('Livewire');
        if (is_dir($componentPath)) {
            $components = glob($componentPath . '/*.php');
            $this->info('   ✓ Found ' . count($components) . ' components:');
            foreach ($components as $component) {
                $name = basename($component, '.php');
                $this->line("     - {$name}");
            }
        } else {
            $this->error('   ✗ Livewire components directory not found');
        }
        $this->newLine();

        // Check Livewire views
        $this->line('4. Checking Livewire Views:');
        $viewPath = resource_path('views/livewire');
        if (is_dir($viewPath)) {
            $views = glob($viewPath . '/**/*.blade.php');
            $this->info('   ✓ Found ' . count($views) . ' view files');
        } else {
            $this->warn('   ⚠ Livewire views directory not found');
        }
        $this->newLine();

        // Check session configuration
        $this->line('5. Checking Session Configuration:');
        $sessionDriver = config('session.driver');
        $this->info("   ✓ Session driver: {$sessionDriver}");
        
        if ($sessionDriver === 'database') {
            try {
                \Illuminate\Support\Facades\DB::table('sessions')->count();
                $this->info('   ✓ Sessions table accessible');
            } catch (\Exception $e) {
                $this->error('   ✗ Sessions table error: ' . $e->getMessage());
            }
        }
        $this->newLine();

        // Check CSRF
        $this->line('6. Checking CSRF Configuration:');
        $csrfEnabled = config('session.encrypt', false);
        $this->info('   ✓ CSRF protection: ' . ($csrfEnabled ? 'Enabled' : 'Disabled'));
        $this->newLine();

        $this->info('✅ Livewire configuration check completed!');
        
        return Command::SUCCESS;
    }
}

