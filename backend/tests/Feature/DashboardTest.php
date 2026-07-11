<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard and receive stats', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();

    $response->assertInertia(fn (Assert $page) => $page
        ->component('Dashboard')
        ->has('stats')
        ->has('stats.total_projects')
        ->has('stats.featured_projects')
        ->has('stats.total_posts')
        ->has('stats.published_posts')
        ->has('stats.draft_posts')
        ->has('stats.total_services')
        ->has('stats.active_services')
        ->has('stats.total_messages')
        ->has('stats.unread_messages')
        ->has('stats.total_media')
        ->has('stats.total_blog_views')
        ->has('stats.views_over_time')
        ->has('stats.device_share')
        ->has('stats.top_referrers')
        ->has('stats.recent_messages')
    );
});
