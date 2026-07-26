<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessImageJob;
use App\Models\Media;
use App\Services\HashidsHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class MediaController extends Controller
{
    /**
     * Display a listing of the media assets.
     */
    public function index(Request $request)
    {
        $query = Media::query();

        // Search by filename or alt text
        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('original_filename', 'like', "%{$search}%")
                    ->orWhere('alt_text', 'like', "%{$search}%");
            });
        }

        // Filter by file type
        if ($request->filled('type')) {
            $type = $request->input('type');
            if ($type === 'svg') {
                $query->where(function ($q) {
                    $q->where('mime_type', 'image/svg+xml')
                        ->orWhere('mime_type', 'image/svg');
                });
            } elseif ($type === 'png') {
                $query->where('mime_type', 'image/png');
            } elseif ($type === 'jpeg') {
                $query->whereIn('mime_type', ['image/jpeg', 'image/jpg']);
            } elseif ($type === 'webp') {
                $query->where('mime_type', 'image/webp');
            }
        }

        // Sorting
        $sort = $request->input('sort', 'latest');
        if ($sort === 'oldest') {
            $query->oldest();
        } elseif ($sort === 'name') {
            $query->orderBy('original_filename', 'asc');
        } elseif ($sort === 'size_desc') {
            $query->orderBy('file_size', 'desc');
        } else {
            $query->latest();
        }

        $perPage = (int) $request->input('per_page', 24);
        if ($perPage <= 0 || $perPage > 100) {
            $perPage = 24;
        }

        $media = $query->paginate($perPage)->withQueryString();

        if ($request->wantsJson()) {
            return response()->json($media);
        }

        return Inertia::render('media/Index', [
            'media' => $media,
            'filters' => [
                'q' => $request->input('q', ''),
                'type' => $request->input('type', ''),
                'sort' => $sort,
                'per_page' => $perPage,
            ],
        ]);
    }

    /**
     * Store a newly created media asset in storage.
     */
    public function store(Request $request)
    {
        if ($request->has('filename')) {
            $request->merge([
                'filename' => Str::slug($request->input('filename')),
            ]);
        }

        $request->validate([
            'file' => 'required_without:files|file|image|max:10240',
            'files.*' => 'file|image|max:10240',
            'filename' => 'nullable|string|regex:/^[a-z0-9\-]+$/|max:255',
            'alt_text' => 'nullable|string|max:255',
        ]);

        $uploadedMedia = [];
        $files = $request->hasFile('files') ? $request->file('files') : [$request->file('file')];

        foreach ($files as $file) {
            if ($file && $file->isValid()) {
                $originalName = $file->getClientOriginalName();
                $mimeType = $file->getMimeType();
                $fileSize = $file->getSize();

                // SVG is vector-based, width/height is 0 by default and skipped from processing
                $width = 0;
                $height = 0;
                if ($mimeType !== 'image/svg+xml' && $mimeType !== 'image/svg') {
                    $dimensions = @getimagesize($file->getRealPath());
                    if ($dimensions) {
                        $width = $dimensions[0];
                        $height = $dimensions[1];
                    }
                }

                // Slugify filename
                if ($request->filled('filename')) {
                    $filename = Str::slug($request->input('filename'));
                } else {
                    $filenameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
                    $filename = Str::slug($filenameWithoutExt);
                }

                // Save to private media disk
                $storedPath = $file->store('originals', 'media');

                // Create initial DB record
                $media = Media::create([
                    'original_filename' => $originalName,
                    'filename' => $filename,
                    'storage_path' => $storedPath,
                    'mime_type' => $mimeType,
                    'file_size' => $fileSize,
                    'width' => $width,
                    'height' => $height,
                    'alt_text' => $request->input('alt_text'),
                    'variants' => [
                        'original' => [
                            'path' => $storedPath,
                            'width' => $width,
                            'height' => $height,
                        ],
                    ],
                ]);

                // Dispatch resizing & converting job for regular images
                if ($mimeType !== 'image/svg+xml' && $mimeType !== 'image/svg') {
                    ProcessImageJob::dispatch($media);
                }

                $media->refresh();
                $uploadedMedia[] = $media;
            }
        }

        return response()->json([
            'message' => __('Upload successful'),
            'media' => count($uploadedMedia) === 1 ? $uploadedMedia[0] : $uploadedMedia,
        ], 201);
    }

    /**
     * Update the specified media asset's alt text in storage.
     */
    public function update(Request $request, Media $media)
    {
        $request->validate([
            'alt_text' => 'required|string|max:255',
        ]);

        $media->update([
            'alt_text' => $request->input('alt_text'),
        ]);

        return response()->json([
            'message' => __('Alt text updated successfully'),
            'media' => $media,
        ]);
    }

    /**
     * Check if the media asset is being referenced by other modules.
     */
    public function usage(Media $media)
    {
        $usages = [];

        // Map tables and columns referencing media table
        $relations = [
            'projects' => ['column' => 'cover_image_id', 'label' => 'Proyek'],
            'project_images' => ['column' => 'media_id', 'label' => 'Galeri Proyek'],
            'posts' => ['column' => 'cover_image_id', 'label' => 'Artikel Blog'],
            'technologies' => ['column' => 'logo_media_id', 'label' => 'Teknologi'],
            'experiences' => ['column' => 'logo_media_id', 'label' => 'Pengalaman'],
            'site_settings' => ['column' => 'profile_photo_id', 'label' => 'Pengaturan Profil'],
        ];

        foreach ($relations as $table => $info) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, $info['column'])) {
                $count = DB::table($table)->where($info['column'], $media->id)->count();
                if ($count > 0) {
                    $usages[] = [
                        'type' => $table,
                        'label' => $info['label'],
                        'count' => $count,
                    ];
                }
            }
        }

        return response()->json([
            'in_use' => count($usages) > 0,
            'usages' => $usages,
        ]);
    }

    /**
     * Remove the specified media asset from storage.
     */
    public function destroy(Media $media)
    {
        $disk = Storage::disk('media');

        // Delete original file
        $originalPath = $media->variants['original']['path'] ?? null;
        if ($originalPath && $disk->exists($originalPath)) {
            $disk->delete($originalPath);
        }

        // Delete converted WebP file
        if ($media->storage_path && $disk->exists($media->storage_path)) {
            $disk->delete($media->storage_path);
        }

        // Delete size variants
        if ($media->variants) {
            foreach ($media->variants as $name => $variant) {
                if ($name !== 'original' && isset($variant['path']) && $disk->exists($variant['path'])) {
                    $disk->delete($variant['path']);
                }
            }
        }

        $media->delete();

        return response()->json([
            'message' => __('Media deleted successfully'),
        ]);
    }

    /**
     * Securely stream a media file or its variant.
     */
    public function show($slug_id, $variant = 'webp')
    {
        // Parse the encoded_id from the end of the slug (slug-encoded_id)
        $lastHyphenPos = strrpos($slug_id, '-');
        $encoded_id = $lastHyphenPos !== false ? substr($slug_id, $lastHyphenPos + 1) : $slug_id;

        $id = HashidsHelper::decode($encoded_id);
        if (! $id) {
            abort(404);
        }

        $media = Media::findOrFail($id);
        $disk = Storage::disk('media');
        $filePath = null;

        if ($variant === 'original') {
            $filePath = $media->variants['original']['path'] ?? $media->storage_path;
        } elseif ($variant === 'webp') {
            $filePath = $media->storage_path;
        } else {
            $filePath = $media->variants[$variant]['path'] ?? null;
        }

        // Fallback to primary webp if variant doesn't exist
        if (! $filePath || ! $disk->exists($filePath)) {
            $filePath = $media->storage_path;
        }

        if (! $disk->exists($filePath)) {
            abort(404);
        }

        // Stream file using buffered chunks
        return response()->stream(function () use ($disk, $filePath) {
            $stream = $disk->readStream($filePath);
            if (is_resource($stream)) {
                fpassthru($stream);
                fclose($stream);
            }
        }, 200, [
            'Content-Type' => $disk->mimeType($filePath),
            'Content-Length' => $disk->size($filePath),
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }
}
