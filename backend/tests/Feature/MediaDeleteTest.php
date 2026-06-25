<?php

use App\Models\Media;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

test('guests cannot check usage or delete media', function () {
    $media = Media::create([
        'original_filename' => 'photo.jpg',
        'filename' => 'photo',
        'storage_path' => 'webp/photo.webp',
        'mime_type' => 'image/webp',
        'file_size' => 100,
        'width' => 600,
        'height' => 400,
    ]);

    $responseUsage = $this->get(route('media.usage', $media));
    $responseUsage->assertStatus(302)->assertRedirect(route('login'));

    $responseDelete = $this->delete(route('media.destroy', $media));
    $responseDelete->assertStatus(302)->assertRedirect(route('login'));
});

test('administrator can check media usage', function () {
    $user = User::factory()->create();
    $media = Media::create([
        'original_filename' => 'photo.jpg',
        'filename' => 'photo',
        'storage_path' => 'webp/photo.webp',
        'mime_type' => 'image/webp',
        'file_size' => 100,
        'width' => 600,
        'height' => 400,
    ]);

    $response = $this->actingAs($user)
        ->getJson(route('media.usage', $media));

    $response->assertStatus(200);
    $response->assertJson([
        'in_use' => false,
        'usages' => [],
    ]);
});

test('administrator can delete media and clean up files from storage', function () {
    Storage::fake('media');

    $originalPath = 'originals/photo.jpg';
    $webpPath = 'webp/photo.webp';
    $thumbnailPath = 'variants/thumbnail/photo.webp';

    Storage::disk('media')->put($originalPath, 'raw data');
    Storage::disk('media')->put($webpPath, 'raw data');
    Storage::disk('media')->put($thumbnailPath, 'raw data');

    $user = User::factory()->create();
    $media = Media::create([
        'original_filename' => 'photo.jpg',
        'filename' => 'photo',
        'storage_path' => $webpPath,
        'mime_type' => 'image/webp',
        'file_size' => 100,
        'width' => 600,
        'height' => 400,
        'variants' => [
            'original' => [
                'path' => $originalPath,
                'width' => 600,
                'height' => 400,
            ],
            'thumbnail' => [
                'path' => $thumbnailPath,
                'width' => 300,
                'height' => 200,
            ],
        ],
    ]);

    Storage::disk('media')->assertExists($originalPath);
    Storage::disk('media')->assertExists($webpPath);
    Storage::disk('media')->assertExists($thumbnailPath);

    $response = $this->actingAs($user)
        ->deleteJson(route('media.destroy', $media));

    $response->assertStatus(200);
    $this->assertDatabaseMissing('media', ['id' => $media->id]);

    // Assert files are deleted
    Storage::disk('media')->assertMissing($originalPath);
    Storage::disk('media')->assertMissing($webpPath);
    Storage::disk('media')->assertMissing($thumbnailPath);
});
