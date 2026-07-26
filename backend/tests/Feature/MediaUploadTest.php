<?php

use App\Jobs\ProcessImageJob;
use App\Models\Media;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

test('guests cannot upload media', function () {
    Storage::fake('media');

    $file = UploadedFile::fake()->image('avatar.jpg');

    $response = $this->post(route('media.store'), [
        'file' => $file,
    ]);

    $response->assertStatus(302);
    $response->assertRedirect(route('login'));
});

test('administrator can upload a valid image with custom filename and alt text and dispatch process job', function () {
    Storage::fake('media');
    Queue::fake();

    $user = User::factory()->create();
    $file = UploadedFile::fake()->image('project-shot.jpg', 1920, 1080);

    $response = $this->actingAs($user)
        ->postJson(route('media.store'), [
            'file' => $file,
            'filename' => 'my-custom-slug-name',
            'alt_text' => 'This is custom alt text',
        ]);

    $response->assertStatus(201);
    $response->assertJsonStructure([
        'message',
        'media' => [
            'id',
            'original_filename',
            'filename',
            'storage_path',
            'mime_type',
            'file_size',
            'width',
            'height',
            'alt_text',
            'variants',
            'encoded_id',
            'urls',
        ],
    ]);

    $mediaId = $response->json('media.id');

    // Assert custom filename and alt text are saved in DB
    $media = Media::find($mediaId);
    $this->assertEquals('my-custom-slug-name', $media->filename);
    $this->assertEquals('This is custom alt text', $media->alt_text);
    $this->assertStringContainsString('my-custom-slug-name-'.$media->encoded_id, $media->urls['original']);

    // Assert file stored on media disk
    Storage::disk('media')->assertExists($media->storage_path);

    // Assert Queue Job dispatched
    Queue::assertPushed(ProcessImageJob::class, function ($job) use ($media) {
        return $job->media->id === $media->id;
    });
});

test('cannot upload non-image files', function () {
    Storage::fake('media');
    $user = User::factory()->create();
    $file = UploadedFile::fake()->create('report.pdf', 200, 'application/pdf');

    $response = $this->actingAs($user)
        ->post(route('media.store'), [
            'file' => $file,
        ]);

    $response->assertStatus(302);
    $response->assertSessionHasErrors(['file']);
});

test('cannot upload file larger than 10MB', function () {
    Storage::fake('media');
    $user = User::factory()->create();
    // 11000 KB is ~10.7MB
    $file = UploadedFile::fake()->create('giant.jpg', 11000, 'image/jpeg');

    $response = $this->actingAs($user)
        ->post(route('media.store'), [
            'file' => $file,
        ]);

    $response->assertStatus(302);
    $response->assertSessionHasErrors(['file']);
});

test('upload with invalid filename characters automatically slugifies and succeeds', function () {
    Storage::fake('media');
    Queue::fake();
    $user = User::factory()->create();
    $file = UploadedFile::fake()->image('photo.jpg');

    $response = $this->actingAs($user)
        ->postJson(route('media.store'), [
            'file' => $file,
            'filename' => 'My Invalid Name @ Spaces!',
            'alt_text' => 'Alt text',
        ]);

    $response->assertStatus(201);
    $mediaId = $response->json('media.id');
    $media = Media::find($mediaId);

    // Assert it was converted to lowercase and spaces replaced with hyphens
    $this->assertEquals('my-invalid-name-at-spaces', $media->filename);
});
