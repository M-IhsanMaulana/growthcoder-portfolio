<?php

namespace App\Jobs;

use App\Models\Media;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ProcessImageJob implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Media $media) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $disk = Storage::disk('media');

        // Skip SVG or non-manipulable files
        if ($this->media->mime_type === 'image/svg+xml' || $this->media->mime_type === 'image/svg') {
            return;
        }

        $originalPath = $this->media->storage_path;
        if (! $disk->exists($originalPath)) {
            return;
        }

        try {
            $absolutePath = $disk->path($originalPath);
            $manager = new ImageManager(new Driver);

            // 1. Convert original to WebP (main image file)
            $img = $manager->decode($absolutePath);
            $webpData = $img->encodeUsingFileExtension('webp', 80);

            $baseName = pathinfo($originalPath, PATHINFO_FILENAME);
            $webpPath = 'webp/'.$baseName.'.webp';
            $disk->put($webpPath, (string) $webpData);

            // 2. Generate variants (thumbnail, medium, large)
            $sizes = [
                'thumbnail' => 300,
                'medium' => 800,
                'large' => 1200,
            ];

            $variants = [];

            foreach ($sizes as $name => $width) {
                // Decode a fresh instance of the image to avoid cumulative resizing issues
                $variantImg = $manager->decode($absolutePath);

                // Only scale down, do not upscale if original is smaller
                if ($variantImg->width() > $width) {
                    $variantImg->scale(width: $width);
                }

                $variantData = $variantImg->encodeUsingFileExtension('webp', 85);
                $variantPath = "variants/{$name}/{$baseName}.webp";

                $disk->put($variantPath, (string) $variantData);

                $variants[$name] = [
                    'path' => $variantPath,
                    'width' => $variantImg->width(),
                    'height' => $variantImg->height(),
                ];
            }

            // 3. Update media record with processed data
            $this->media->update([
                'storage_path' => $webpPath,
                'variants' => $variants,
                'width' => $img->width(),
                'height' => $img->height(),
            ]);

        } catch (\Throwable $e) {
            logger()->error('Failed to process image ID '.$this->media->id.': '.$e->getMessage());
            throw $e;
        }
    }
}
