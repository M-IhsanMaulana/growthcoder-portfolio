<?php

use App\Models\Media;
use Illuminate\Support\Facades\Storage;

test('public route can stream a media asset and its variants with caching headers', function () {
    Storage::fake('media');

    // Store a fake image in the disk
    $path = 'webp/fake-image.webp';
    Storage::disk('media')->put($path, 'fake webp raw content');

    $thumbPath = 'variants/thumbnail/fake-image.webp';
    Storage::disk('media')->put($thumbPath, 'fake thumbnail raw content');

    // Create a database entry
    $media = Media::create([
        'original_filename' => 'original.jpg',
        'filename' => 'renamed-file',
        'storage_path' => $path,
        'mime_type' => 'image/webp',
        'file_size' => 100,
        'width' => 600,
        'height' => 400,
        'variants' => [
            'original' => [
                'path' => $path,
                'width' => 600,
                'height' => 400,
            ],
            'thumbnail' => [
                'path' => $thumbPath,
                'width' => 300,
                'height' => 200,
            ],
        ],
    ]);

    $slugId = $media->filename.'-'.$media->encoded_id;

    // Test main webp streaming
    $response = $this->get(route('media.show', ['slug_id' => $slugId]));
    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'image/webp');
    $response->assertHeader('Cache-Control', 'immutable, max-age=31536000, public');
    $this->assertEquals('fake webp raw content', $response->streamedContent());

    // Test thumbnail variant streaming
    $responseThumb = $this->get(route('media.show', ['slug_id' => $slugId, 'variant' => 'thumbnail']));
    $responseThumb->assertStatus(200);
    $responseThumb->assertHeader('Content-Type', 'image/webp');
    $this->assertEquals('fake thumbnail raw content', $responseThumb->streamedContent());
});

test('media stream returns 404 for invalid encoded id', function () {
    $response = $this->get(route('media.show', ['slug_id' => 'somefile-invalid-encoded-string']));
    $response->assertStatus(404);
});

test('media stream returns 404 for non-existent file', function () {
    Storage::fake('media');

    // Create entry pointing to missing file
    $media = Media::create([
        'original_filename' => 'missing.jpg',
        'filename' => 'missing-file',
        'storage_path' => 'webp/missing.webp',
        'mime_type' => 'image/webp',
        'file_size' => 100,
        'width' => 600,
        'height' => 400,
    ]);

    $slugId = $media->filename.'-'.$media->encoded_id;

    $response = $this->get(route('media.show', ['slug_id' => $slugId]));
    $response->assertStatus(404);
});
