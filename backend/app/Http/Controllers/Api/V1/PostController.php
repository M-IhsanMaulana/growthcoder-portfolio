<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\PostStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostDetailResource;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\PostView;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Post::query()
            ->where('status', PostStatus::Published)
            ->with(['categories', 'coverImage'])
            ->orderBy('published_at', 'desc')
            ->orderBy('created_at', 'desc');

        // Search by title or excerpt
        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        // Filter by category slug
        if ($request->filled('category')) {
            $categorySlug = $request->input('category');
            $query->whereHas('categories', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        $posts = $query->paginate($request->integer('per_page', 10));

        return PostResource::collection($posts);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $slug)
    {
        $post = Post::where('slug', $slug)
            ->where('status', PostStatus::Published)
            ->with(['categories', 'coverImage', 'relatedPosts.categories', 'relatedPosts.coverImage'])
            ->firstOrFail();

        // Load the count of views
        $post->loadCount('views');

        // Fallback: If no manual related posts are configured, auto-recommend up to 3 posts from same categories
        if ($post->relatedPosts->isEmpty()) {
            $categoryIds = $post->categories->pluck('id');
            $autoRelated = Post::where('status', PostStatus::Published)
                ->where('id', '!=', $post->id)
                ->whereHas('categories', function ($q) use ($categoryIds) {
                    $q->whereIn('categories.id', $categoryIds);
                })
                ->with(['categories', 'coverImage'])
                ->limit(3)
                ->get();

            $post->setRelation('relatedPosts', $autoRelated);
        }

        // Fetch previous post (older than current post)
        $previousPost = Post::where('status', PostStatus::Published)
            ->where(function ($q) use ($post) {
                $q->where('published_at', '<', $post->published_at)
                    ->orWhere(function ($sub) use ($post) {
                        $sub->where('published_at', $post->published_at)
                            ->where('id', '<', $post->id);
                    });
            })
            ->orderBy('published_at', 'desc')
            ->orderBy('id', 'desc')
            ->first(['title', 'slug']);

        // Fetch next post (newer than current post)
        $nextPost = Post::where('status', PostStatus::Published)
            ->where(function ($q) use ($post) {
                $q->where('published_at', '>', $post->published_at)
                    ->orWhere(function ($sub) use ($post) {
                        $sub->where('published_at', $post->published_at)
                            ->where('id', '>', $post->id);
                    });
            })
            ->orderBy('published_at', 'asc')
            ->orderBy('id', 'asc')
            ->first(['title', 'slug']);

        $post->previous_post = $previousPost;
        $post->next_post = $nextPost;

        return new PostDetailResource($post);
    }

    /**
     * Increment post view count from client-side request.
     */
    public function incrementView(Request $request, string $slug): JsonResponse
    {
        $post = Post::where('slug', $slug)
            ->where('status', PostStatus::Published)
            ->firstOrFail();

        $this->recordView($request, $post);

        return response()->json([
            'success' => true,
            'views_count' => $post->views()->count(),
        ]);
    }

    /**
     * Record a page view with spam and bot prevention.
     */
    private function recordView(Request $request, Post $post): void
    {
        $userAgent = $request->header('User-Agent');

        // 1. Bot Filter: Skip crawlers/bots
        if ($userAgent && preg_match('/bot|crawl|spider|slurp|tracker|click|api|fetch|curl|wget/i', strtolower($userAgent))) {
            return;
        }

        // 2. Cooldown Check: 1 hour unique visit per IP & Post
        $ipHash = hash('sha256', $request->ip());
        $recentViewExists = PostView::where('post_id', $post->id)
            ->where('ip_hash', $ipHash)
            ->where('created_at', '>=', now()->subHour())
            ->exists();

        if ($recentViewExists) {
            return;
        }

        // 3. Detect Device type
        $device = 'desktop';
        if ($userAgent) {
            $uaLower = strtolower($userAgent);
            if (preg_match('/tablet|ipad|playbook|silk/i', $uaLower)) {
                $device = 'tablet';
            } elseif (preg_match('/mobile|phone|iphone|ipod|android|blackberry|opera mini|iemobile/i', $uaLower)) {
                $device = 'mobile';
            }
        }

        // 4. Parse Referrer
        $referrer = 'direct';
        $refererHeader = $request->input('referrer') ?? $request->header('Referer');
        if ($refererHeader) {
            $host = parse_url($refererHeader, PHP_URL_HOST);
            if ($host) {
                $referrer = preg_replace('/^www\./', '', strtolower($host));

                // If referrer is the same as the application host, treat as direct
                $ownHost = parse_url(config('app.url'), PHP_URL_HOST);
                if ($referrer === $ownHost) {
                    $referrer = 'direct';
                }
            }
        }

        // 5. Save View record
        PostView::create([
            'post_id' => $post->id,
            'ip_hash' => $ipHash,
            'user_agent' => $userAgent ? substr($userAgent, 0, 500) : null,
            'device' => $device,
            'referrer' => $referrer,
        ]);
    }
}
