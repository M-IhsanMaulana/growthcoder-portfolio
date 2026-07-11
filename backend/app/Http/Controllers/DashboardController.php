<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\Media;
use App\Models\Post;
use App\Models\PostView;
use App\Models\Project;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): Response
    {
        // KPI Metrics
        $totalProjects = Project::count();
        $featuredProjects = Project::where('is_featured', true)->count();

        $totalPosts = Post::count();
        $publishedPosts = Post::where('status', 'published')->count();
        $draftPosts = Post::where('status', 'draft')->count();

        $totalServices = Service::count();
        $activeServices = Service::where('is_active', true)->count();

        $totalMessages = ContactMessage::count();
        $unreadMessages = ContactMessage::where('status', 'unread')->count();

        $totalMedia = Media::count();

        // 30 Days daily stats (aggregated across all posts)
        $viewsOverTime = PostView::where('created_at', '>=', now()->subDays(30))
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
            'labels' => array_map(function (string $date): string {
                return date('d M', strtotime($date));
            }, array_keys($dates)),
            'values' => array_values($dates),
        ];

        // Device Share (aggregated across all posts)
        $deviceShareRaw = PostView::select('device', DB::raw('count(*) as count'))
            ->groupBy('device')
            ->get()
            ->pluck('count', 'device')
            ->toArray();

        $deviceShare = array_merge([
            'desktop' => 0,
            'mobile' => 0,
            'tablet' => 0,
        ], $deviceShareRaw);

        // Top Referrers (aggregated across all posts)
        $topReferrers = PostView::select('referrer', DB::raw('count(*) as count'))
            ->groupBy('referrer')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get()
            ->toArray();

        // Latest 5 Contact Messages
        $recentMessages = ContactMessage::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return Inertia::render('Dashboard', [
            'stats' => [
                'total_projects' => $totalProjects,
                'featured_projects' => $featuredProjects,
                'total_posts' => $totalPosts,
                'published_posts' => $publishedPosts,
                'draft_posts' => $draftPosts,
                'total_services' => $totalServices,
                'active_services' => $activeServices,
                'total_messages' => $totalMessages,
                'unread_messages' => $unreadMessages,
                'total_media' => $totalMedia,
                'total_blog_views' => PostView::count(),
                'views_over_time' => $chartData,
                'device_share' => $deviceShare,
                'top_referrers' => $topReferrers,
                'recent_messages' => $recentMessages,
            ],
        ]);
    }
}
