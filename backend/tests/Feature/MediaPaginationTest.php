<?php

use App\Models\Media;
use App\Models\User;

test('guests cannot view media index', function () {
    $response = $this->get(route('media.index'));

    $response->assertStatus(302);
    $response->assertRedirect(route('login'));
});

test('administrator can view paginated media list', function () {
    $user = User::factory()->create();

    // Create 30 media records
    for ($i = 1; $i <= 30; $i++) {
        Media::create([
            'original_filename' => "sample-image-{$i}.png",
            'filename' => "sample-image-{$i}",
            'storage_path' => "media/sample-image-{$i}.png",
            'mime_type' => 'image/png',
            'file_size' => 1024 * $i,
            'width' => 800,
            'height' => 600,
            'alt_text' => "Sample Image {$i}",
        ]);
    }

    $response = $this->actingAs($user)
        ->getJson(route('media.index', ['per_page' => 10]));

    $response->assertStatus(200);
    $response->assertJsonPath('total', 30);
    $response->assertJsonPath('per_page', 10);
    $response->assertJsonPath('current_page', 1);
    $response->assertJsonPath('last_page', 3);
    $this->assertCount(10, $response->json('data'));
});

test('media listing can be filtered by file type', function () {
    $user = User::factory()->create();

    Media::create([
        'original_filename' => 'icon.svg',
        'filename' => 'icon',
        'storage_path' => 'media/icon.svg',
        'mime_type' => 'image/svg+xml',
        'file_size' => 500,
        'width' => 0,
        'height' => 0,
    ]);

    Media::create([
        'original_filename' => 'photo.jpg',
        'filename' => 'photo',
        'storage_path' => 'media/photo.jpg',
        'mime_type' => 'image/jpeg',
        'file_size' => 1500,
        'width' => 1200,
        'height' => 800,
    ]);

    $response = $this->actingAs($user)
        ->getJson(route('media.index', ['type' => 'svg']));

    $response->assertStatus(200);
    $response->assertJsonPath('total', 1);
    $response->assertJsonPath('data.0.mime_type', 'image/svg+xml');
});

test('media listing can be sorted', function () {
    $user = User::factory()->create();

    Media::create([
        'original_filename' => 'a-first.png',
        'filename' => 'a-first',
        'storage_path' => 'media/a-first.png',
        'mime_type' => 'image/png',
        'file_size' => 100,
        'width' => 100,
        'height' => 100,
    ]);

    Media::create([
        'original_filename' => 'z-last.png',
        'filename' => 'z-last',
        'storage_path' => 'media/z-last.png',
        'mime_type' => 'image/png',
        'file_size' => 500,
        'width' => 100,
        'height' => 100,
    ]);

    $response = $this->actingAs($user)
        ->getJson(route('media.index', ['sort' => 'name']));

    $response->assertStatus(200);
    $response->assertJsonPath('data.0.original_filename', 'a-first.png');
});
