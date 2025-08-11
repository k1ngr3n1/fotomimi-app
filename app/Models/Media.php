<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'filename',
        'filepath',
        'category',
        'type', // 'photo' or 'video'
        'file_size',
        'dimensions',
        'alt_text',
        'is_featured',
        'sort_order'
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
        'file_size' => 'integer',
    ];

    protected $appends = ['url', 'thumbnail_url'];

    public function getUrlAttribute()
    {
        try {
            return Storage::disk('main_disk')->url($this->filepath);
        } catch (\Exception $e) {
            // Fallback to local storage if main_disk is not available
            return asset('storage/' . $this->filepath);
        }
    }

    public function getThumbnailUrlAttribute()
    {
        // For now, return the same URL. You can implement thumbnail generation later
        return $this->url;
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopePhotos($query)
    {
        return $query->where('type', 'photo');
    }

    public function scopeVideos($query)
    {
        return $query->where('type', 'video');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
} 