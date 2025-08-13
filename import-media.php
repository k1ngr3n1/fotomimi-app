<?php

require_once 'vendor/autoload.php';

use App\Models\Media;
use Illuminate\Support\Facades\Storage;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Media Import Script ===\n\n";

// Configuration
$baseDirectory = 'C:/path/to/your/photos'; // Change this to your actual path
$categories = ['wedding', 'baptism', 'concert', 'studio', 'modelling', 'travel'];

$totalImported = 0;

foreach ($categories as $category) {
    $categoryPath = $baseDirectory . '/' . $category;
    
    if (!is_dir($categoryPath)) {
        echo "Skipping {$category} - directory not found: {$categoryPath}\n";
        continue;
    }
    
    echo "Processing {$category}...\n";
    
    $files = glob($categoryPath . '/*');
    $importedCount = 0;
    
    foreach ($files as $file) {
        if (is_file($file)) {
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            $filename = pathinfo($file, PATHINFO_FILENAME);
            
            // Determine if it's a photo or video
            $photoExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'tiff'];
            $videoExtensions = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm', 'mkv'];
            
            if (in_array($extension, $photoExtensions)) {
                $type = 'photo';
            } elseif (in_array($extension, $videoExtensions)) {
                $type = 'video';
            } else {
                echo "Skipping unsupported file: {$file}\n";
                continue;
            }
            
            // Copy file to storage
            $storagePath = $type === 'photo' ? "photos/{$category}" : "videos/{$category}";
            $newFilename = $filename . '_' . time() . '.' . $extension;
            $filepath = $storagePath . '/' . $newFilename;
            
            try {
                // Try main_disk first, fallback to public disk
                try {
                    Storage::disk('main_disk')->put($filepath, file_get_contents($file));
                } catch (Exception $e) {
                    echo "Error with main_disk, trying public disk: " . $e->getMessage() . "\n";
                    Storage::disk('public')->put($filepath, file_get_contents($file));
                }
                
                // Get file info
                $fileSize = filesize($file);
                $dimensions = null;
                
                if ($type === 'photo') {
                    $imageInfo = getimagesize($file);
                    if ($imageInfo) {
                        $dimensions = $imageInfo[0] . 'x' . $imageInfo[1];
                    }
                }
                
                // Create media record
                Media::create([
                    'title' => ucfirst(str_replace(['_', '-'], ' ', $filename)),
                    'description' => null,
                    'filename' => $newFilename,
                    'filepath' => $filepath,
                    'category' => $category,
                    'type' => $type,
                    'file_size' => $fileSize,
                    'dimensions' => $dimensions,
                    'alt_text' => ucfirst(str_replace(['_', '-'], ' ', $filename)),
                    'is_featured' => false,
                    'sort_order' => 0
                ]);
                
                $importedCount++;
                echo "Imported: {$filename}.{$extension}\n";
                
            } catch (Exception $e) {
                echo "Error importing {$file}: " . $e->getMessage() . "\n";
            }
        }
    }
    
    echo "Imported {$importedCount} files to {$category} category.\n\n";
    $totalImported += $importedCount;
}

echo "=== Import Complete ===\n";
echo "Total files imported: {$totalImported}\n"; 