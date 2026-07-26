<?php

test('returns a redirect response for guest', function () {
    $response = $this->get(route('home'));

    $response->assertRedirect(route('login'));
});
