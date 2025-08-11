<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function dashboard()
    {
        try {
            // Get statistics
            $stats = [
                'total_images' => Media::photos()->count(),
                'total_videos' => Media::videos()->count(),
                'featured_media' => Media::where('is_featured', true)->count(),
                'total_media' => Media::count(),
            ];

            // Get recent uploads
            $recentUploads = Media::orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($media) {
                    try {
                        return [
                            'id' => $media->id,
                            'title' => $media->title,
                            'category' => $media->category,
                            'type' => $media->type,
                            'uploaded_at' => $media->created_at->diffForHumans(),
                            'thumbnail' => $media->type === 'photo' ? $media->url : null
                        ];
                    } catch (\Exception $e) {
                        \Log::error('Error processing media item', [
                            'media_id' => $media->id,
                            'error' => $e->getMessage()
                        ]);
                        
                        return [
                            'id' => $media->id,
                            'title' => $media->title,
                            'category' => $media->category,
                            'type' => $media->type,
                            'uploaded_at' => $media->created_at->diffForHumans(),
                            'thumbnail' => null,
                            'error' => 'Failed to load thumbnail'
                        ];
                    }
                });

            return Inertia::render('admin/Dashboard', [
                'stats' => $stats,
                'recentUploads' => $recentUploads
            ]);
        } catch (\Exception $e) {
            \Log::error('Dashboard error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return Inertia::render('admin/Dashboard', [
                'stats' => [
                    'total_images' => 0,
                    'total_videos' => 0,
                    'featured_media' => 0,
                    'total_media' => 0,
                ],
                'recentUploads' => [],
                'error' => 'Failed to load dashboard data'
            ]);
        }
    }

    public function settings()
    {
        return Inertia::render('admin/Settings');
    }
} 