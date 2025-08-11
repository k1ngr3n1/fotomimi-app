<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class TestStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test storage configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing storage configuration...');
        
        // Test main_disk configuration
        $this->info('Testing main_disk...');
        try {
            $config = config('filesystems.disks.main_disk');
            $this->info('Main disk config: ' . json_encode($config, JSON_PRETTY_PRINT));
            
            // Test if disk is accessible
            $testFile = 'test_' . time() . '.txt';
            $testContent = 'Test content ' . date('Y-m-d H:i:s');
            
            Storage::disk('main_disk')->put($testFile, $testContent);
            $this->info('✓ Successfully wrote to main_disk');
            
            $readContent = Storage::disk('main_disk')->get($testFile);
            if ($readContent === $testContent) {
                $this->info('✓ Successfully read from main_disk');
            } else {
                $this->warn('⚠ Read content does not match written content');
            }
            
            // Test URL generation
            $url = Storage::disk('main_disk')->url($testFile);
            $this->info('✓ URL generated: ' . $url);
            
            // Clean up
            Storage::disk('main_disk')->delete($testFile);
            $this->info('✓ Successfully deleted test file');
            
        } catch (\Exception $e) {
            $this->error('✗ Error with main_disk: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
        }
        
        // Test public disk as fallback
        $this->info('Testing public disk...');
        try {
            $testFile = 'test_public_' . time() . '.txt';
            $testContent = 'Test content ' . date('Y-m-d H:i:s');
            
            Storage::disk('public')->put($testFile, $testContent);
            $this->info('✓ Successfully wrote to public disk');
            
            $readContent = Storage::disk('public')->get($testFile);
            if ($readContent === $testContent) {
                $this->info('✓ Successfully read from public disk');
            } else {
                $this->warn('⚠ Read content does not match written content');
            }
            
            // Test URL generation
            $url = Storage::disk('public')->url($testFile);
            $this->info('✓ URL generated: ' . $url);
            
            // Clean up
            Storage::disk('public')->delete($testFile);
            $this->info('✓ Successfully deleted test file');
            
        } catch (\Exception $e) {
            $this->error('✗ Error with public disk: ' . $e->getMessage());
        }
        
        // Check environment variables
        $this->info('Environment variables:');
        $this->info('MAIN_STORAGE_DRIVER: ' . env('MAIN_STORAGE_DRIVER', 'not set'));
        $this->info('AWS_ACCESS_KEY_ID: ' . (env('AWS_ACCESS_KEY_ID') ? 'set' : 'not set'));
        $this->info('AWS_SECRET_ACCESS_KEY: ' . (env('AWS_SECRET_ACCESS_KEY') ? 'set' : 'not set'));
        $this->info('AWS_BUCKET: ' . (env('AWS_BUCKET') ? 'set' : 'not set'));
        $this->info('AWS_DEFAULT_REGION: ' . (env('AWS_DEFAULT_REGION') ? 'set' : 'not set'));
        
        $this->info('Storage test completed!');
    }
}
