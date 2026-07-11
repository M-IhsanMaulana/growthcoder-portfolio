<?php

namespace Database\Seeders;

use App\Models\Media;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait CreatesMedia
{
    /**
     * Create a dummy media record and ensure a physical placeholder image exists.
     */
    protected function createDummyMedia(string $originalFilename, string $altText, ?string $filename = null): Media
    {
        $filename = $filename ?? Str::slug(pathinfo($originalFilename, PATHINFO_FILENAME));
        $ext = pathinfo($originalFilename, PATHINFO_EXTENSION) ?: 'webp';
        $storagePath = 'originals/'.$filename.'.'.$ext;

        // Ensure the directory exists
        if (! Storage::disk('media')->exists('originals')) {
            Storage::disk('media')->makeDirectory('originals');
        }

        // Ensure the dummy file exists in storage
        if (! Storage::disk('media')->exists($storagePath)) {
            // A 1x1 transparent WebP image base64
            $dummyWebp = base64_decode('UklGRhoAAABXRUJQVlA4TA0AAAAvAAAAEAcQERGIiP8H');
            Storage::disk('media')->put($storagePath, $dummyWebp);
        }

        return Media::create([
            'original_filename' => $originalFilename,
            'filename' => $filename,
            'storage_path' => $storagePath,
            'mime_type' => 'image/'.($ext === 'webp' ? 'webp' : 'png'),
            'file_size' => 100,
            'width' => 100,
            'height' => 100,
            'alt_text' => $altText,
            'variants' => [
                'original' => [
                    'path' => $storagePath,
                    'width' => 100,
                    'height' => 100,
                ],
            ],
        ]);
    }
}
