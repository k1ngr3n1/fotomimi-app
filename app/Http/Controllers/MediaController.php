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
                'studio' => 'Studio',
                'modelling' => 'Modelling'
            ]
        ]);
    }

    // Admin methods
    public function adminIndex(Request $request)
    {
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
                'studio' => 'Studio',
                'modelling' => 'Modelling',
                'other' => 'Other'
            ],
            'filters' => $request->only(['search', 'category', 'type', 'featured'])
        ]);
    }

    public function adminUpload()
    {
        return Inertia::render('admin/Upload');
    }

    public function store(Request $request)
    {
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'required|file|mimes:jpeg,jpg,png,gif,webp,bmp,tiff,mp4,avi,mov,wmv,flv,webm,mkv|max:102400', // 100MB max
            'category' => 'required|in:wedding,baptism,concert,studio,modelling,other',
            'titles.*' => 'nullable|string|max:255',
            'descriptions.*' => 'nullable|string',
            'alt_texts.*' => 'nullable|string|max:255',
            'is_featured.*' => 'boolean',
            'sort_orders.*' => 'integer'
        ]);

        $uploadedCount = 0;
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
            'request_data' => $request->all()
        ]);

        // Check if files were uploaded
        if (!$files || !is_array($files)) {
            return back()->withErrors(['files' => 'No files were uploaded.']);
        }

        foreach ($files as $index => $file) {
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
                continue; // Skip unsupported files
            }
            
            // Generate unique filename
            $newFilename = $filename . '_' . time() . '_' . $index . '.' . $extension;
            $storagePath = $type === 'photo' ? "photos/{$request->category}" : "videos/{$request->category}";
            $filepath = $storagePath . '/' . $newFilename;
            
            // Store file
            Storage::disk('main_disk')->put($filepath, file_get_contents($file));
            
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
        }
        
        return redirect()->route('admin.media')->with('success', "Successfully uploaded {$uploadedCount} files.");
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
        // Delete file from storage
        Storage::disk('main_disk')->delete($media->filepath);
        
        // Delete database record
        $media->delete();
        
        return back()->with('success', 'Media deleted successfully.');
    }

    public function importFromDirectory(Request $request)
    {
        $request->validate([
            'directory_path' => 'required|string',
            'category' => 'required|in:wedding,baptism,concert,studio,modelling,other'
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
                
                Storage::disk('main_disk')->put($filepath, file_get_contents($file));
                
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

        $categories = ['wedding', 'baptism', 'concert', 'studio', 'modelling'];
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