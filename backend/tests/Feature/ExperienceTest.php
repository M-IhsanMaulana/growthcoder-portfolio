<?php

use App\Models\Experience;
use App\Models\Media;
use App\Models\User;

test('guests cannot access experiences CMS routes', function () {
    $response = $this->get('/admin-cms/education-experience');
    $response->assertStatus(302);
    $response->assertRedirect(route('login'));
});

test('administrator can view experiences CMS dashboard', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->get('/admin-cms/education-experience');

    $response->assertStatus(200);
});

test('administrator can create a work experience', function () {
    $user = User::factory()->create();
    $media = Media::create([
        'original_filename' => 'company.png',
        'filename' => 'company',
        'storage_path' => 'media/company.png',
        'mime_type' => 'image/png',
        'file_size' => 1024,
        'width' => 100,
        'height' => 100,
    ]);

    $response = $this->actingAs($user)
        ->post('/admin-cms/experiences', [
            'company' => 'PT. Solusi Digital',
            'title_position' => 'Senior Developer',
            'location' => 'Jakarta (Remote)',
            'start_date' => '2024-01',
            'end_date' => '',
            'description' => 'Working with Laravel.',
            'website_url' => 'https://solusidigital.id',
            'logo_media_id' => $media->id,
            'order' => 1,
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('experiences', [
        'company' => 'PT. Solusi Digital',
        'title_position' => 'Senior Developer',
        'location' => 'Jakarta (Remote)',
        'end_date' => null,
        'description' => 'Working with Laravel.',
        'website_url' => 'https://solusidigital.id',
        'logo_media_id' => $media->id,
        'order' => 1,
    ]);

    $exp = Experience::first();
    $this->assertEquals('2024-01-01', $exp->start_date->format('Y-m-d'));
});

test('administrator can update a work experience', function () {
    $user = User::factory()->create();
    $exp = Experience::create([
        'company' => 'Old Company',
        'title_position' => 'Junior Dev',
        'start_date' => '2023-01-01',
        'order' => 0,
    ]);

    $response = $this->actingAs($user)
        ->put("/admin-cms/experiences/{$exp->id}", [
            'company' => 'New Company',
            'title_position' => 'Mid Dev',
            'location' => 'Bandung',
            'start_date' => '2023-01',
            'end_date' => '2024-05',
            'description' => 'Promoted.',
            'website_url' => 'https://newcompany.com',
            'logo_media_id' => null,
            'order' => 2,
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('experiences', [
        'id' => $exp->id,
        'company' => 'New Company',
        'title_position' => 'Mid Dev',
        'location' => 'Bandung',
        'description' => 'Promoted.',
        'website_url' => 'https://newcompany.com',
        'logo_media_id' => null,
        'order' => 2,
    ]);

    $exp = $exp->fresh();
    $this->assertEquals('2023-01-01', $exp->start_date->format('Y-m-d'));
    $this->assertEquals('2024-05-01', $exp->end_date->format('Y-m-d'));
});

test('administrator can delete a work experience', function () {
    $user = User::factory()->create();
    $exp = Experience::create([
        'company' => 'Temporary Co',
        'title_position' => 'Contractor',
        'start_date' => '2022-01-01',
        'order' => 0,
    ]);

    $response = $this->actingAs($user)
        ->delete("/admin-cms/experiences/{$exp->id}");

    $response->assertRedirect();
    $this->assertDatabaseMissing('experiences', [
        'id' => $exp->id,
    ]);
});

test('administrator can reorder work experiences', function () {
    $user = User::factory()->create();
    $exp1 = Experience::create([
        'company' => 'Company A',
        'title_position' => 'Developer',
        'start_date' => '2022-01-01',
        'order' => 10,
    ]);
    $exp2 = Experience::create([
        'company' => 'Company B',
        'title_position' => 'Developer',
        'start_date' => '2023-01-01',
        'order' => 20,
    ]);

    $response = $this->actingAs($user)
        ->post('/admin-cms/experiences/reorder', [
            'ids' => [$exp2->id, $exp1->id],
        ]);

    $response->assertRedirect();
    $this->assertEquals(0, $exp2->fresh()->order);
    $this->assertEquals(1, $exp1->fresh()->order);
});

test('public api can list experiences', function () {
    $exp = Experience::create([
        'company' => 'Tech Corp',
        'title_position' => 'Lead',
        'start_date' => '2020-01-01',
        'order' => 0,
    ]);

    $response = $this->getJson('/api/v1/experiences');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment([
            'company' => 'Tech Corp',
            'title_position' => 'Lead',
            'start_date' => '2020-01',
            'end_date' => null,
        ]);
});
