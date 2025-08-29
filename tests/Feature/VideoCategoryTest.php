<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Media;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class VideoCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_video_category_is_available_in_upload_validation()
    {
        $user = User::factory()->create(['approved' => true, 'is_superadmin' => true]);
        
        $this->actingAs($user);
        
        $response = $this->get(route('admin.upload'));
        
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('admin/Upload')
        );
    }

    public function test_video_category_can_be_used_for_media_upload()
    {
        $user = User::factory()->create(['approved' => true, 'is_superadmin' => true]);
        
        $this->actingAs($user);
        
        Storage::fake('public');
        
        $file = UploadedFile::fake()->create('test_video.mp4', 1024);
        
        $response = $this->post(route('admin.media.store'), [
            'files' => [$file],
            'category' => 'video',
            'titles' => ['Test Video'],
            'descriptions' => ['Test video description'],
            'alt_texts' => ['Test video alt text'],
            'is_featured' => [false],
            'sort_orders' => [0]
        ]);
        
        $response->assertRedirect(route('admin.media'));
        
        $this->assertDatabaseHas('media', [
            'category' => 'video',
            'type' => 'video',
            'title' => 'Test Video'
        ]);
    }

    public function test_video_category_is_included_in_database_enum()
    {
        $media = Media::create([
            'title' => 'Test Video',
            'description' => 'Test description',
            'filename' => 'test_video.mp4',
            'filepath' => 'videos/video/test_video.mp4',
            'category' => 'video',
            'type' => 'video',
            'file_size' => 1024,
            'alt_text' => 'Test video',
            'is_featured' => false,
            'sort_order' => 0
        ]);
        
        $this->assertDatabaseHas('media', [
            'id' => $media->id,
            'category' => 'video'
        ]);
    }
}
