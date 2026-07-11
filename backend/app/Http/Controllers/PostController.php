<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\PostView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Post::query()->with(['categories', 'coverImage'])
            ->orderBy('created_at', 'desc');

        // Search by title or excerpt
        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->input('category_id'));
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $posts = $query->get();
        $categories = Category::orderBy('name', 'asc')->get();

        return Inertia::render('posts/Index', [
            'posts' => $posts,
            'categories' => $categories,
            'filters' => $request->only(['q', 'category_id', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::orderBy('name', 'asc')->get();
        // Load existing published/draft posts for manual related posts selection
        $posts = Post::orderBy('title', 'asc')->get(['id', 'title', 'slug']);

        return Inertia::render('posts/Create', [
            'categories' => $categories,
            'posts' => $posts,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:posts,slug|regex:/^[a-z0-9\-]+$/',
            'excerpt' => 'nullable|string|max:1000',
            'content' => 'required|string',
            'status' => 'required|in:draft,published,scheduled',
            'scheduled_at' => 'required_if:status,scheduled|nullable|date|after:now',
            'cover_image_id' => 'nullable|exists:media,id',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'exists:categories,id',
            'related_post_ids' => 'nullable|array',
            'related_post_ids.*' => 'exists:posts,id',
        ]);

        $slug = $request->filled('slug')
            ? Str::slug($request->input('slug'))
            : Str::slug($request->input('title'));

        // Handle slug collision
        $originalSlug = $slug;
        $count = 1;
        while (Post::where('slug', $slug)->exists()) {
            $slug = $originalSlug.'-'.$count++;
        }

        $publishedAt = null;
        if ($request->input('status') === 'published') {
            $publishedAt = now();
        }

        DB::beginTransaction();
        try {
            $post = Post::create([
                'title' => $request->input('title'),
                'slug' => $slug,
                'excerpt' => $request->input('excerpt'),
                'content' => $request->input('content'),
                'status' => $request->input('status'),
                'published_at' => $publishedAt,
                'scheduled_at' => $request->input('status') === 'scheduled' ? $request->input('scheduled_at') : null,
                'cover_image_id' => $request->input('cover_image_id'),
                'meta_title' => $request->input('meta_title'),
                'meta_description' => $request->input('meta_description'),
            ]);

            // Sync categories
            $post->categories()->sync($request->input('category_ids'));

            // Sync manual related posts
            if ($request->has('related_post_ids')) {
                $post->relatedPosts()->sync($request->input('related_post_ids'));
            }

            DB::commit();

            Inertia::flash('toast', [
                'type' => 'success',
                'message' => __('Artikel berhasil disimpan.'),
            ]);

            return redirect()->route('posts.index');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->withErrors(['error' => __('Gagal menyimpan artikel: ').$e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        $post->load(['categories', 'coverImage']);

        // Get 30 days daily stats
        $viewsOverTime = PostView::where('post_id', $post->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Fill empty days with 0
        $dates = [];
        for ($i = 29; $i >= 0; $i--) {
            $dates[now()->subDays($i)->format('Y-m-d')] = 0;
        }
        foreach ($viewsOverTime as $view) {
            if (isset($dates[$view->date])) {
                $dates[$view->date] = (int) $view->count;
            }
        }

        $chartData = [
            'labels' => array_map(function ($date) {
                return date('d M', strtotime($date));
            }, array_keys($dates)),
            'values' => array_values($dates),
        ];

        // Device Share
        $deviceShareRaw = PostView::where('post_id', $post->id)
            ->select('device', DB::raw('count(*) as count'))
            ->groupBy('device')
            ->get()
            ->pluck('count', 'device')
            ->toArray();

        $deviceShare = array_merge([
            'desktop' => 0,
            'mobile' => 0,
            'tablet' => 0,
        ], $deviceShareRaw);

        // Top Referrers
        $topReferrers = PostView::where('post_id', $post->id)
            ->select('referrer', DB::raw('count(*) as count'))
            ->groupBy('referrer')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        return Inertia::render('posts/Show', [
            'post' => $post,
            'stats' => [
                'total_views' => $post->views()->count(),
                'unique_visitors' => $post->views()->distinct('ip_hash')->count(),
                'views_over_time' => $chartData,
                'device_share' => $deviceShare,
                'top_referrers' => $topReferrers,
            ],
        ]);
    }

    /**
     * Preview the post with typography layout.
     */
    public function preview(Post $post)
    {
        $post->load(['categories', 'coverImage']);

        return Inertia::render('posts/Preview', [
            'post' => $post,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        $post->load(['categories', 'coverImage', 'relatedPosts']);
        $categories = Category::orderBy('name', 'asc')->get();
        // Load other posts (excluding current post)
        $posts = Post::where('id', '!=', $post->id)
            ->orderBy('title', 'asc')
            ->get(['id', 'title', 'slug']);

        return Inertia::render('posts/Edit', [
            'post' => $post,
            'categories' => $categories,
            'posts' => $posts,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:posts,slug,'.$post->id.'|regex:/^[a-z0-9\-]+$/',
            'excerpt' => 'nullable|string|max:1000',
            'content' => 'required|string',
            'status' => 'required|in:draft,published,scheduled',
            'scheduled_at' => 'required_if:status,scheduled|nullable|date|after:now',
            'cover_image_id' => 'nullable|exists:media,id',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'exists:categories,id',
            'related_post_ids' => 'nullable|array',
            'related_post_ids.*' => 'exists:posts,id',
        ]);

        $slug = Str::slug($request->input('slug'));

        // Handle slug collision
        $originalSlug = $slug;
        $count = 1;
        while (Post::where('slug', $slug)->where('id', '!=', $post->id)->exists()) {
            $slug = $originalSlug.'-'.$count++;
        }

        DB::beginTransaction();
        try {
            $status = $request->input('status');
            $publishedAt = $post->published_at;
            $scheduledAt = $post->scheduled_at;

            if ($status === 'published') {
                if (is_null($publishedAt)) {
                    $publishedAt = now();
                }
                $scheduledAt = null;
            } elseif ($status === 'scheduled') {
                $scheduledAt = $request->input('scheduled_at');
                $publishedAt = null;
            } else {
                $publishedAt = null;
                $scheduledAt = null;
            }

            $post->update([
                'title' => $request->input('title'),
                'slug' => $slug,
                'excerpt' => $request->input('excerpt'),
                'content' => $request->input('content'),
                'status' => $status,
                'published_at' => $publishedAt,
                'scheduled_at' => $scheduledAt,
                'cover_image_id' => $request->input('cover_image_id'),
                'meta_title' => $request->input('meta_title'),
                'meta_description' => $request->input('meta_description'),
            ]);

            // Sync categories
            $post->categories()->sync($request->input('category_ids'));

            // Sync manual related posts
            if ($request->has('related_post_ids')) {
                $post->relatedPosts()->sync($request->input('related_post_ids'));
            } else {
                $post->relatedPosts()->detach();
            }

            DB::commit();

            Inertia::flash('toast', [
                'type' => 'success',
                'message' => __('Artikel berhasil diperbarui.'),
            ]);

            return redirect()->route('posts.index');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->withErrors(['error' => __('Gagal memperbarui artikel: ').$e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource in storage.
     */
    public function destroy(Post $post)
    {
        $post->delete();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Artikel berhasil dihapus.'),
        ]);

        return redirect()->route('posts.index');
    }
}
