<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to the login page from API docs', function () {
    $response = $this->get(route('api-docs.index'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the API docs and see the component', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('api-docs.index'));
    $response->assertOk();

    $response->assertInertia(fn (Assert $page) => $page
        ->component('ApiDocs')
    );
});
