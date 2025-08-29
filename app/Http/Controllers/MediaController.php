<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->get('category', 'all');
        $type = $request->get('type', 'all');
        
        $query = Media::query();
        
        if ($category !== 'all') {
            $query->byCategory($category);
        }
        
        if ($type === 'photo') {
            $query->photos();
        } elseif ($type === 'video') {
            $query->videos();
        }
        
        $media = $query->orderBy('sort_order')->orderBy('created_at', 'desc')->get();
        
        return Inertia::render('Gallery', [
            'category' => $category,
            'media' => $media,
            'categories' => [
                'all' => 'All Photos',
                'wedding' => 'Weddings',
                'baptism' => 'Baptisms', 
                'concert' => 'Concerts',
                'on-set' => 'On Set',
                'studio' => 'Studio',
                'modelling' => 'Modelling',
                'travel' => 'Travel'
            ]
        ]);
    }

    // Admin methods
    public function adminIndex(Request $request)
    {
        try {
            $query = Media::query();
            
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }
            
            if ($request->filled('category')) {
                $query->byCategory($request->get('category'));
            }
            
            if ($request->filled('type')) {
                if ($request->get('type') === 'image') {
                    $query->photos();
                } elseif ($request->get('type') === 'video') {
                    $query->videos();
                }
            }
            
            if ($request->filled('featured')) {
                $query->where('is_featured', $request->get('featured'));
            }
            
            $media = $query->orderBy('created_at', 'desc')->get();
            
            return Inertia::render('admin/Media', [
                'media' => $media,
                'categories' => [
                    'wedding' => 'Weddings',
                    'baptism' => 'Baptisms',
                    'concert' => 'Concerts',
                    'on-set' => 'On Set',
                    'studio' => 'Studio',
                    'modelling' => 'Modelling',
                    'travel' => 'Travel',
                    'other' => 'Other'
                ],
                'filters' => $request->only(['search', 'category', 'type', 'featured'])
            ]);
        } catch (\Exception $e) {
            \Log::error('Admin media index error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return Inertia::render('admin/Media', [
                'media' => [],
                'categories' => [
                    'wedding' => 'Weddings',
                    'baptism' => 'Baptisms',
                    'concert' => 'Concerts',
                    'on-set' => 'On Set',
                    'studio' => 'Studio',
                    'modelling' => 'Modelling',
                    'travel' => 'Travel',
                    'other' => 'Other'
                ],
                'filters' => $request->only(['search', 'category', 'type', 'featured']),
                'error' => 'Failed to load media data'
            ]);
        }
    }

    public function adminUpload()
    {
        return Inertia::render('admin/Upload');
    }

    public function store(Request $request)
    {
        $request->validate([
            'files' => 'required|array|max:50', // Allow up to 50 files
            'files.*' => 'required|file|mimes:jpeg,jpg,png,gif,webp,bmp,tiff,mp4,avi,mov,wmv,flv,webm,mkv|max:102400', // 100MB max per file
            'category' => 'required|in:wedding,baptism,concert,on-set,studio,modelling,travel,video,other',
            'titles.*' => 'nullable|string|max:255',
            'descriptions.*' => 'nullable|string',
            'alt_texts.*' => 'nullable|string|max:255',
            'is_featured.*' => 'boolean',
            'sort_orders.*' => 'integer'
        ]);

        $uploadedCount = 0;
        $failedCount = 0;
        $errors = [];
        $files = $request->file('files');
        $titles = $request->input('titles', []);
        $descriptions = $request->input('descriptions', []);
        $altTexts = $request->input('alt_texts', []);
        $isFeatured = $request->input('is_featured', []);
        $sortOrders = $request->input('sort_orders', []);

        // Debug logging
        \Log::info('Upload request received', [
            'files_count' => $files ? count($files) : 0,
            'category' => $request->category,
            'max_files_allowed' => 50
        ]);

        // Check if files were uploaded
        if (!$files || !is_array($files)) {
            return back()->withErrors(['files' => 'No files were uploaded.']);
        }

        // Check file count limit
        if (count($files) > 50) {
            return back()->withErrors(['files' => 'Maximum 50 files can be uploaded at once.']);
        }

        foreach ($files as $index => $file) {
            try {
                $extension = strtolower($file->getClientOriginalExtension());
                $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                
                // Determine if it's a photo or video
                $photoExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'tiff'];
                $videoExtensions = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm', 'mkv'];
                
                if (in_array($extension, $photoExtensions)) {
                    $type = 'photo';
                } elseif (in_array($extension, $videoExtensions)) {
                    $type = 'video';
                } else {
                    $failedCount++;
                    $errors[] = "File {$file->getClientOriginalName()} has unsupported extension: {$extension}";
                    continue; // Skip unsupported files
                }
                
                // Generate unique filename
                $newFilename = $filename . '_' . time() . '_' . $index . '.' . $extension;
                $storagePath = $type === 'photo' ? "photos/{$request->category}" : "videos/{$request->category}";
                $filepath = $storagePath . '/' . $newFilename;
                
                // Store file
                try {
                    Storage::disk('main_disk')->put($filepath, file_get_contents($file));
                } catch (\Exception $e) {
                    \Log::error('File upload failed', [
                        'file' => $file->getClientOriginalName(),
                        'error' => $e->getMessage(),
                        'disk' => 'main_disk'
                    ]);
                    
                    // Fallback to public disk if main_disk fails
                    Storage::disk('public')->put($filepath, file_get_contents($file));
                }
                
                // Get file info
                $fileSize = $file->getSize();
                $dimensions = null;
                
                if ($type === 'photo') {
                    $imageInfo = getimagesize($file->getPathname());
                    if ($imageInfo) {
                        $dimensions = $imageInfo[0] . 'x' . $imageInfo[1];
                    }
                }
                
                // Create media record
                Media::create([
                    'title' => $titles[$index] ?? ucfirst(str_replace(['_', '-'], ' ', $filename)),
                    'description' => $descriptions[$index] ?? null,
                    'filename' => $newFilename,
                    'filepath' => $filepath,
                    'category' => $request->category,
                    'type' => $type,
                    'file_size' => $fileSize,
                    'dimensions' => $dimensions,
                    'alt_text' => $altTexts[$index] ?? ucfirst(str_replace(['_', '-'], ' ', $filename)),
                    'is_featured' => isset($isFeatured[$index]) ? (bool)$isFeatured[$index] : false,
                    'sort_order' => $sortOrders[$index] ?? 0
                ]);
                
                $uploadedCount++;
                
            } catch (\Exception $e) {
                $failedCount++;
                $errors[] = "Failed to upload {$file->getClientOriginalName()}: " . $e->getMessage();
                \Log::error('File upload error', [
                    'file' => $file->getClientOriginalName(),
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        $message = "Successfully uploaded {$uploadedCount} files.";
        if ($failedCount > 0) {
            $message .= " Failed to upload {$failedCount} files.";
        }
        
        if (!empty($errors)) {
            return back()->withErrors(['upload_errors' => $errors])->with('success', $message);
        }
        
        return redirect()->route('admin.media')->with('success', $message);
    }

    public function edit(Media $media)
    {
        return Inertia::render('admin/EditMedia', [
            'media' => $media
        ]);
    }

    public function update(Request $request, Media $media)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'alt_text' => 'nullable|string|max:255',
            'is_featured' => 'boolean',
            'sort_order' => 'integer'
        ]);

        $media->update($request->only([
            'title', 'description', 'alt_text', 'is_featured', 'sort_order'
        ]));

        return back()->with('success', 'Media updated successfully.');
    }

    public function destroy(Media $media)
    {
        try {
            // Log the deletion attempt
            \Log::info('Attempting to delete media', [
                'media_id' => $media->id,
                'filepath' => $media->filepath,
                'title' => $media->title,
                'request_method' => request()->method(),
                'user_id' => auth()->id()
            ]);

            // Delete file from storage
            $fileDeleted = false;
            try {
                if (Storage::disk('main_disk')->exists($media->filepath)) {
                    Storage::disk('main_disk')->delete($media->filepath);
                    $fileDeleted = true;
                    \Log::info('File deleted from main_disk', ['filepath' => $media->filepath]);
                }
            } catch (\Exception $e) {
                \Log::error('File deletion failed from main_disk', [
                    'file' => $media->filepath,
                    'error' => $e->getMessage(),
                    'disk' => 'main_disk'
                ]);
                
                // Fallback to public disk if main_disk fails
                try {
                    if (Storage::disk('public')->exists($media->filepath)) {
                        Storage::disk('public')->delete($media->filepath);
                        $fileDeleted = true;
                        \Log::info('File deleted from public disk (fallback)', ['filepath' => $media->filepath]);
                    }
                } catch (\Exception $e2) {
                    \Log::error('File deletion failed from public disk', [
                        'file' => $media->filepath,
                        'error' => $e2->getMessage(),
                        'disk' => 'public'
                    ]);
                }
            }

            // Delete database record
            $media->delete();
            
            \Log::info('Media deleted successfully', [
                'media_id' => $media->id,
                'file_deleted' => $fileDeleted
            ]);

            return back()->with('success', 'Media deleted successfully.');
            
        } catch (\Exception $e) {
            \Log::error('Media deletion failed', [
                'media_id' => $media->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withErrors(['error' => 'Failed to delete media: ' . $e->getMessage()]);
        }
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:media,id'
        ]);

        $ids = $request->input('ids');
        $deletedCount = 0;
        $failedCount = 0;
        $errors = [];

        \Log::info('Attempting bulk delete', [
            'count' => count($ids),
            'ids' => $ids,
            'user_id' => auth()->id()
        ]);

        foreach ($ids as $id) {
            try {
                $media = Media::find($id);
                if (!$media) {
                    $failedCount++;
                    $errors[] = "Media with ID {$id} not found";
                    continue;
                }

                // Delete file from storage
                $fileDeleted = false;
                try {
                    if (Storage::disk('main_disk')->exists($media->filepath)) {
                        Storage::disk('main_disk')->delete($media->filepath);
                        $fileDeleted = true;
                    }
                } catch (\Exception $e) {
                    \Log::error('File deletion failed from main_disk', [
                        'file' => $media->filepath,
                        'error' => $e->getMessage(),
                        'disk' => 'main_disk'
                    ]);
                    
                    // Fallback to public disk if main_disk fails
                    try {
                        if (Storage::disk('public')->exists($media->filepath)) {
                            Storage::disk('public')->delete($media->filepath);
                            $fileDeleted = true;
                        }
                    } catch (\Exception $e2) {
                        \Log::error('File deletion failed from public disk', [
                            'file' => $media->filepath,
                            'error' => $e2->getMessage(),
                            'disk' => 'public'
                        ]);
                    }
                }

                // Delete database record
                $media->delete();
                $deletedCount++;

                \Log::info('Media deleted in bulk operation', [
                    'media_id' => $id,
                    'file_deleted' => $fileDeleted
                ]);

            } catch (\Exception $e) {
                $failedCount++;
                $errors[] = "Failed to delete media {$id}: " . $e->getMessage();
                
                \Log::error('Media deletion failed in bulk operation', [
                    'media_id' => $id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        \Log::info('Bulk delete completed', [
            'total_requested' => count($ids),
            'deleted_count' => $deletedCount,
            'failed_count' => $failedCount
        ]);

        if ($failedCount > 0) {
            return back()->withErrors([
                'bulk_delete' => "Deleted {$deletedCount} items, failed to delete {$failedCount} items.",
                'bulk_errors' => $errors
            ]);
        }

        return back()->with('success', "Successfully deleted {$deletedCount} media items.");
    }

    public function importFromDirectory(Request $request)
    {
        $request->validate([
            'directory_path' => 'required|string',
            'category' => 'required|in:wedding,baptism,concert,on-set,studio,modelling,travel,video,other'
        ]);

        $directoryPath = $request->input('directory_path');
        $category = $request->input('category');
        
        if (!is_dir($directoryPath)) {
            return back()->withErrors(['directory_path' => 'Directory not found']);
        }

        $importedCount = 0;
        $files = glob($directoryPath . '/*');
        
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
                    continue; // Skip unsupported files
                }
                
                // Copy file to storage
                $storagePath = $type === 'photo' ? "photos/{$category}" : "videos/{$category}";
                $newFilename = $filename . '_' . time() . '.' . $extension;
                $filepath = $storagePath . '/' . $newFilename;
                
                try {
                    Storage::disk('main_disk')->put($filepath, file_get_contents($file));
                } catch (\Exception $e) {
                    \Log::error('File import failed', [
                        'file' => $file,
                        'error' => $e->getMessage(),
                        'disk' => 'main_disk'
                    ]);
                    
                    // Fallback to public disk if main_disk fails
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
            }
        }
        
        return back()->with('success', "Successfully imported {$importedCount} files to {$category} category.");
    }

    public function bulkImport(Request $request)
    {
        $request->validate([
            'base_directory' => 'required|string'
        ]);

        $baseDirectory = $request->input('base_directory');
        
        if (!is_dir($baseDirectory)) {
            return back()->withErrors(['base_directory' => 'Base directory not found']);
        }

        $categories = ['wedding', 'baptism', 'concert', 'on-set', 'studio', 'modelling', 'travel', 'video'];
        $totalImported = 0;
        
        foreach ($categories as $category) {
            $categoryPath = $baseDirectory . '/' . $category;
            
            if (is_dir($categoryPath)) {
                $request->merge(['directory_path' => $categoryPath, 'category' => $category]);
                $this->importFromDirectory($request);
                $totalImported++;
            }
        }
        
        return back()->with('success', "Bulk import completed. Processed {$totalImported} categories.");
    }
} 