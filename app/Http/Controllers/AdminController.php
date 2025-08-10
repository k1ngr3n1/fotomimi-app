<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function dashboard()
    {
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
                return [
                    'id' => $media->id,
                    'title' => $media->title,
                    'category' => $media->category,
                    'type' => $media->type,
                    'uploaded_at' => $media->created_at->diffForHumans(),
                    'thumbnail' => $media->type === 'photo' ? $media->url : null
                ];
            });

        return Inertia::render('admin/Dashboard', [
            'stats' => $stats,
            'recentUploads' => $recentUploads
        ]);
    }

    public function settings()
    {
        return Inertia::render('admin/Settings');
    }
} 