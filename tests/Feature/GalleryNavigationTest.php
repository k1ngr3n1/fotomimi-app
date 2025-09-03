<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Media;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GalleryNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_gallery_route_accepts_category_parameter()
    {
        $response = $this->get(route('gallery', 'wedding'));
        
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('Gallery')
                ->where('category', 'wedding')
        );
    }

    public function test_gallery_route_without_category_parameter_works()
    {
        $response = $this->get(route('gallery'));
        
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('Gallery')
                ->where('category', 'all')
        );
    }

    public function test_gallery_filters_media_by_category()
    {
        // Create test media with different categories
        Media::create([
            'title' => 'Wedding Photo 1',
            'description' => 'Beautiful wedding photo',
            'filename' => 'wedding1.jpg',
            'filepath' => 'photos/wedding/wedding1.jpg',
            'category' => 'wedding',
            'type' => 'photo',
            'file_size' => 1024,
            'alt_text' => 'Wedding photo',
            'is_featured' => false,
            'sort_order' => 0
        ]);

        Media::create([
            'title' => 'Baptism Photo 1',
            'description' => 'Beautiful baptism photo',
            'filename' => 'baptism1.jpg',
            'filepath' => 'photos/baptism/baptism1.jpg',
            'category' => 'baptism',
            'type' => 'photo',
            'file_size' => 1024,
            'alt_text' => 'Baptism photo',
            'is_featured' => false,
            'sort_order' => 0
        ]);

        $response = $this->get(route('gallery', 'wedding'));
        
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('Gallery')
                ->where('category', 'wedding')
                ->where('media.0.category', 'wedding')
        );
    }

    public function test_all_categories_are_supported()
    {
        $categories = ['wedding', 'baptism', 'video', 'studio', 'modelling', 'concert'];
        
        foreach ($categories as $category) {
            $response = $this->get(route('gallery', $category));
            $response->assertStatus(200);
            $response->assertInertia(fn ($page) => 
                $page->component('Gallery')
                    ->where('category', $category)
            );
        }
    }

    public function test_wedding_category_shows_wedding_images()
    {
        // Create a wedding image
        Media::create([
            'title' => 'Beautiful Wedding',
            'description' => 'A beautiful wedding ceremony',
            'filename' => 'wedding1.jpg',
            'filepath' => 'photos/wedding/wedding1.jpg',
            'category' => 'wedding',
            'type' => 'photo',
            'file_size' => 1024,
            'alt_text' => 'Wedding photo',
            'is_featured' => false,
            'sort_order' => 0
        ]);

        // Create a non-wedding image
        Media::create([
            'title' => 'Baptism Photo',
            'description' => 'A baptism ceremony',
            'filename' => 'baptism1.jpg',
            'filepath' => 'photos/baptism/baptism1.jpg',
            'category' => 'baptism',
            'type' => 'photo',
            'file_size' => 1024,
            'alt_text' => 'Baptism photo',
            'is_featured' => false,
            'sort_order' => 0
        ]);

        $response = $this->get(route('gallery', 'wedding'));
        
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('Gallery')
                ->where('category', 'wedding')
                ->where('media.0.category', 'wedding')
                ->where('media.0.title', 'Beautiful Wedding')
        );
        
        // Verify all media is returned (frontend handles filtering)
        $this->assertCount(2, $response->viewData('page')['props']['media']);
    }

    public function test_category_counts_are_calculated_from_all_media()
    {
        // Create media in different categories
        Media::create([
            'title' => 'Wedding Photo 1',
            'description' => 'Wedding photo',
            'filename' => 'wedding1.jpg',
            'filepath' => 'photos/wedding/wedding1.jpg',
            'category' => 'wedding',
            'type' => 'photo',
            'file_size' => 1024,
            'alt_text' => 'Wedding photo',
            'is_featured' => false,
            'sort_order' => 0
        ]);

        Media::create([
            'title' => 'Wedding Photo 2',
            'description' => 'Another wedding photo',
            'filename' => 'wedding2.jpg',
            'filepath' => 'photos/wedding/wedding2.jpg',
            'category' => 'wedding',
            'type' => 'photo',
            'file_size' => 1024,
            'alt_text' => 'Wedding photo',
            'is_featured' => false,
            'sort_order' => 0
        ]);

        Media::create([
            'title' => 'Video 1',
            'description' => 'A video',
            'filename' => 'video1.mp4',
            'filepath' => 'videos/video/video1.mp4',
            'category' => 'video',
            'type' => 'video',
            'file_size' => 1024,
            'alt_text' => 'Video',
            'is_featured' => false,
            'sort_order' => 0
        ]);

        Media::create([
            'title' => 'Baptism Photo',
            'description' => 'Baptism photo',
            'filename' => 'baptism1.jpg',
            'filepath' => 'photos/baptism/baptism1.jpg',
            'category' => 'baptism',
            'type' => 'photo',
            'file_size' => 1024,
            'alt_text' => 'Baptism photo',
            'is_featured' => false,
            'sort_order' => 0
        ]);

        // Test that all media is returned regardless of category parameter
        $response = $this->get(route('gallery', 'video'));
        
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('Gallery')
                ->where('category', 'video')
        );
        
        // Verify all 4 media items are returned (frontend handles filtering)
        $this->assertCount(4, $response->viewData('page')['props']['media']);
        
        // Verify the media contains items from different categories
        $media = $response->viewData('page')['props']['media'];
        $categories = collect($media)->pluck('category')->unique()->values()->toArray();
        $this->assertContains('wedding', $categories);
        $this->assertContains('video', $categories);
        $this->assertContains('baptism', $categories);
    }
}
