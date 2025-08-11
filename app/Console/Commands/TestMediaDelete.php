<?php

namespace App\Console\Commands;

use App\Models\Media;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class TestMediaDelete extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:test-delete {media_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test media deletion functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $mediaId = $this->argument('media_id');
        
        if ($mediaId) {
            $media = Media::find($mediaId);
            if (!$media) {
                $this->error("Media with ID {$mediaId} not found.");
                return 1;
            }
            $this->testDeleteMedia($media);
        } else {
            $this->info('Testing media deletion for all media items...');
            $mediaItems = Media::all();
            
            if ($mediaItems->isEmpty()) {
                $this->info('No media items found to test.');
                return 0;
            }
            
            foreach ($mediaItems as $media) {
                $this->testDeleteMedia($media);
                break; // Only test the first one
            }
        }
        
        return 0;
    }
    
    private function testDeleteMedia(Media $media)
    {
        $this->info("Testing deletion for media ID: {$media->id}");
        $this->info("Title: {$media->title}");
        $this->info("Filepath: {$media->filepath}");
        $this->info("Type: {$media->type}");
        
        // Check if file exists in storage
        $this->info('Checking file existence...');
        $existsInMain = Storage::disk('main_disk')->exists($media->filepath);
        $existsInPublic = Storage::disk('public')->exists($media->filepath);
        
        $this->info("File exists in main_disk: " . ($existsInMain ? 'Yes' : 'No'));
        $this->info("File exists in public disk: " . ($existsInPublic ? 'Yes' : 'No'));
        
        if (!$this->confirm('Do you want to proceed with deletion?')) {
            $this->info('Deletion cancelled.');
            return;
        }
        
        try {
            // Simulate the deletion process
            $this->info('Attempting to delete file from storage...');
            
            $fileDeleted = false;
            if ($existsInMain) {
                Storage::disk('main_disk')->delete($media->filepath);
                $this->info('✓ File deleted from main_disk');
                $fileDeleted = true;
            } elseif ($existsInPublic) {
                Storage::disk('public')->delete($media->filepath);
                $this->info('✓ File deleted from public disk');
                $fileDeleted = true;
            } else {
                $this->warn('⚠ File not found in any storage disk');
            }
            
            // Delete database record
            $media->delete();
            $this->info('✓ Database record deleted');
            
            $this->info('✓ Media deletion completed successfully');
            
        } catch (\Exception $e) {
            $this->error('✗ Deletion failed: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
        }
    }
}
