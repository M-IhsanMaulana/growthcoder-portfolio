<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Technology;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Project::query()->with(['category', 'coverImage', 'technologies'])
            ->orderBy('order', 'asc')
            ->orderBy('created_at', 'desc');

        // Search by title
        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where('title', 'like', "%{$search}%");
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $projects = $query->get();
        $categories = ProjectCategory::orderBy('order', 'asc')->get();

        return Inertia::render('projects/Index', [
            'projects' => $projects,
            'categories' => $categories,
            'filters' => $request->only(['q', 'category_id', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = ProjectCategory::orderBy('order', 'asc')->get();
        $technologies = Technology::orderBy('name', 'asc')->get();

        return Inertia::render('projects/Create', [
            'categories' => $categories,
            'technologies' => $technologies,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:projects,slug|regex:/^[a-z0-9\-]+$/',
            'role' => 'nullable|string|max:255',
            'short_description' => 'required|string|max:1000',
            'full_description' => 'nullable|string',
            'category_id' => 'required|exists:project_categories,id',
            'cover_image_id' => 'nullable|exists:media,id',
            'cover_image_caption' => 'nullable|string|max:255',
            'status' => 'required|in:draft,published',
            'is_featured' => 'required|boolean',
            'order' => 'required|integer|min:0',
            'live_url' => 'nullable|url|max:255',
            'github_url' => 'nullable|url|max:255',
            'telegram_url' => 'nullable|url|max:255',
            'technology_ids' => 'nullable|array',
            'technology_ids.*' => 'exists:technologies,id',
            'key_features' => 'nullable|array',
            'key_features.*.title' => 'required|string|max:255',
            'key_features.*.description' => 'nullable|string',
            'key_features.*.icon' => 'nullable|string|max:100',
            'gallery' => 'nullable|array',
            'gallery.*.media_id' => 'required|exists:media,id',
            'gallery.*.order' => 'required|integer|min:0',
            'gallery.*.caption' => 'nullable|string|max:255',
        ], [
            'live_url.url' => __('Format URL tidak valid, gunakan format https://...'),
            'github_url.url' => __('Format URL tidak valid, gunakan format https://...'),
            'telegram_url.url' => __('Format URL tidak valid, gunakan format https://...'),
        ]);

        $slug = $request->filled('slug')
            ? Str::slug($request->input('slug'))
            : Str::slug($request->input('title'));

        // Handle collision for generated slug
        $originalSlug = $slug;
        $count = 1;
        while (Project::where('slug', $slug)->exists()) {
            $slug = $originalSlug.'-'.$count++;
        }

        $publishedAt = null;
        if ($request->input('status') === 'published') {
            $publishedAt = now();
        }

        DB::beginTransaction();
        try {
            $project = Project::create([
                'title' => $request->input('title'),
                'slug' => $slug,
                'role' => $request->input('role'),
                'short_description' => $request->input('short_description'),
                'full_description' => $request->input('full_description'),
                'category_id' => $request->input('category_id'),
                'cover_image_id' => $request->input('cover_image_id'),
                'cover_image_caption' => $request->input('cover_image_caption'),
                'key_features' => $request->input('key_features'),
                'status' => $request->input('status'),
                'is_featured' => $request->input('is_featured'),
                'order' => $request->input('order'),
                'live_url' => $request->input('live_url'),
                'github_url' => $request->input('github_url'),
                'telegram_url' => $request->input('telegram_url'),
                'published_at' => $publishedAt,
            ]);

            if ($request->has('technology_ids')) {
                $project->technologies()->sync($request->input('technology_ids'));
            }

            // Sync gallery images
            if ($request->has('gallery')) {
                $galleryData = [];
                foreach ($request->input('gallery') as $item) {
                    $galleryData[$item['media_id']] = [
                        'order' => $item['order'],
                        'caption' => $item['caption'],
                    ];
                }
                $project->galleryImages()->sync($galleryData);
            }

            DB::commit();

            Inertia::flash('toast', [
                'type' => 'success',
                'message' => __('Proyek berhasil disimpan.'),
            ]);

            return redirect()->route('projects.edit', $project);

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->withErrors(['error' => __('Gagal menyimpan proyek: ').$e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        $project->load(['category', 'coverImage', 'technologies', 'galleryImages']);
        $categories = ProjectCategory::orderBy('order', 'asc')->get();
        $technologies = Technology::orderBy('name', 'asc')->get();

        return Inertia::render('projects/Edit', [
            'project' => $project,
            'categories' => $categories,
            'technologies' => $technologies,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:projects,slug,'.$project->id.'|regex:/^[a-z0-9\-]+$/',
            'role' => 'nullable|string|max:255',
            'short_description' => 'required|string|max:1000',
            'full_description' => 'nullable|string',
            'category_id' => 'required|exists:project_categories,id',
            'cover_image_id' => 'nullable|exists:media,id',
            'cover_image_caption' => 'nullable|string|max:255',
            'status' => 'required|in:draft,published',
            'is_featured' => 'required|boolean',
            'order' => 'required|integer|min:0',
            'live_url' => 'nullable|url|max:255',
            'github_url' => 'nullable|url|max:255',
            'telegram_url' => 'nullable|url|max:255',
            'technology_ids' => 'nullable|array',
            'technology_ids.*' => 'exists:technologies,id',
            'key_features' => 'nullable|array',
            'key_features.*.title' => 'required|string|max:255',
            'key_features.*.description' => 'nullable|string',
            'key_features.*.icon' => 'nullable|string|max:100',
            'gallery' => 'nullable|array',
            'gallery.*.media_id' => 'required|exists:media,id',
            'gallery.*.order' => 'required|integer|min:0',
            'gallery.*.caption' => 'nullable|string|max:255',
        ], [
            'live_url.url' => __('Format URL tidak valid, gunakan format https://...'),
            'github_url.url' => __('Format URL tidak valid, gunakan format https://...'),
            'telegram_url.url' => __('Format URL tidak valid, gunakan format https://...'),
        ]);

        $publishedAt = $project->published_at;
        if ($request->input('status') === 'published' && $project->status !== 'published') {
            $publishedAt = now();
        } elseif ($request->input('status') === 'draft') {
            $publishedAt = null;
        }

        DB::beginTransaction();
        try {
            $project->update([
                'title' => $request->input('title'),
                'slug' => Str::slug($request->input('slug')),
                'role' => $request->input('role'),
                'short_description' => $request->input('short_description'),
                'full_description' => $request->input('full_description'),
                'category_id' => $request->input('category_id'),
                'cover_image_id' => $request->input('cover_image_id'),
                'cover_image_caption' => $request->input('cover_image_caption'),
                'key_features' => $request->input('key_features'),
                'status' => $request->input('status'),
                'is_featured' => $request->input('is_featured'),
                'order' => $request->input('order'),
                'live_url' => $request->input('live_url'),
                'github_url' => $request->input('github_url'),
                'telegram_url' => $request->input('telegram_url'),
                'published_at' => $publishedAt,
            ]);

            // Sync tech stack
            if ($request->has('technology_ids')) {
                $project->technologies()->sync($request->input('technology_ids'));
            } else {
                $project->technologies()->detach();
            }

            // Sync gallery images
            if ($request->has('gallery')) {
                $galleryData = [];
                foreach ($request->input('gallery') as $item) {
                    $galleryData[$item['media_id']] = [
                        'order' => $item['order'],
                        'caption' => $item['caption'],
                    ];
                }
                $project->galleryImages()->sync($galleryData);
            } else {
                $project->galleryImages()->detach();
            }

            DB::commit();

            Inertia::flash('toast', [
                'type' => 'success',
                'message' => __('Proyek berhasil diperbarui.'),
            ]);

            return redirect()->back();

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->withErrors(['error' => __('Gagal memperbarui proyek: ').$e->getMessage()]);
        }
    }

    /**
     * Display the specified resource for preview.
     */
    public function show(Project $project)
    {
        $project->load(['category', 'coverImage', 'technologies', 'galleryImages']);

        return Inertia::render('projects/Show', [
            'project' => $project,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $project->delete();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Proyek berhasil dihapus.'),
        ]);

        return redirect()->route('projects.index');
    }

    /**
     * Reorder projects via drag-and-drop.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:projects,id',
        ]);

        $ids = $request->input('ids');

        DB::transaction(function () use ($ids) {
            foreach ($ids as $index => $id) {
                Project::where('id', $id)->update(['order' => $index]);
            }
        });

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Urutan proyek berhasil diperbarui.'),
        ]);

        return redirect()->back();
    }
}
