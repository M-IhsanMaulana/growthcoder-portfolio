<?php

use App\Models\Education;
use App\Models\Media;
use App\Models\User;

test('guests cannot access educations CMS routes', function () {
    $response = $this->get('/admin-cms/education-experience');
    $response->assertStatus(302);
    $response->assertRedirect(route('login'));
});

test('administrator can view educations CMS dashboard', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->get('/admin-cms/education-experience');

    $response->assertStatus(200);
});

test('administrator can create an education record', function () {
    $user = User::factory()->create();
    $media = Media::create([
        'original_filename' => 'school.png',
        'filename' => 'school',
        'storage_path' => 'media/school.png',
        'mime_type' => 'image/png',
        'file_size' => 1024,
        'width' => 100,
        'height' => 100,
    ]);

    $response = $this->actingAs($user)
        ->post('/admin-cms/educations', [
            'institution' => 'Universitas Brawijaya',
            'degree' => 'S1',
            'major' => 'Teknik Informatika',
            'gpa' => '3.85',
            'location' => 'Malang, Jawa Timur',
            'start_date' => '2020-09',
            'end_date' => '2024-07',
            'description' => 'Active in organization.',
            'logo_media_id' => $media->id,
            'order' => 1,
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('educations', [
        'institution' => 'Universitas Brawijaya',
        'degree' => 'S1',
        'major' => 'Teknik Informatika',
        'gpa' => '3.85',
        'location' => 'Malang, Jawa Timur',
        'description' => 'Active in organization.',
        'logo_media_id' => $media->id,
        'order' => 1,
    ]);

    $edu = Education::first();
    $this->assertEquals('2020-09-01', $edu->start_date->format('Y-m-d'));
    $this->assertEquals('2024-07-01', $edu->end_date->format('Y-m-d'));
});

test('administrator can update an education record', function () {
    $user = User::factory()->create();
    $edu = Education::create([
        'institution' => 'High School',
        'major' => 'Science',
        'start_date' => '2017-07-01',
        'order' => 0,
    ]);

    $response = $this->actingAs($user)
        ->put("/admin-cms/educations/{$edu->id}", [
            'institution' => 'High School Updated',
            'degree' => 'Diploma',
            'major' => 'Computer Science',
            'gpa' => '4.00',
            'location' => 'Jakarta',
            'start_date' => '2017-07',
            'end_date' => '2020-05',
            'description' => 'Finished.',
            'logo_media_id' => null,
            'order' => 3,
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('educations', [
        'id' => $edu->id,
        'institution' => 'High School Updated',
        'degree' => 'Diploma',
        'major' => 'Computer Science',
        'gpa' => '4.00',
        'location' => 'Jakarta',
        'description' => 'Finished.',
        'logo_media_id' => null,
        'order' => 3,
    ]);

    $edu = $edu->fresh();
    $this->assertEquals('2017-07-01', $edu->start_date->format('Y-m-d'));
    $this->assertEquals('2020-05-01', $edu->end_date->format('Y-m-d'));
});

test('administrator can delete an education record', function () {
    $user = User::factory()->create();
    $edu = Education::create([
        'institution' => 'Delete Me Univ',
        'major' => 'Arts',
        'start_date' => '2019-01-01',
        'order' => 0,
    ]);

    $response = $this->actingAs($user)
        ->delete("/admin-cms/educations/{$edu->id}");

    $response->assertRedirect();
    $this->assertDatabaseMissing('educations', [
        'id' => $edu->id,
    ]);
});

test('administrator can reorder education records', function () {
    $user = User::factory()->create();
    $edu1 = Education::create([
        'institution' => 'School A',
        'major' => 'Major A',
        'start_date' => '2015-01-01',
        'order' => 5,
    ]);
    $edu2 = Education::create([
        'institution' => 'School B',
        'major' => 'Major B',
        'start_date' => '2016-01-01',
        'order' => 6,
    ]);

    $response = $this->actingAs($user)
        ->post('/admin-cms/educations/reorder', [
            'ids' => [$edu2->id, $edu1->id],
        ]);

    $response->assertRedirect();
    $this->assertEquals(0, $edu2->fresh()->order);
    $this->assertEquals(1, $edu1->fresh()->order);
});

test('public api can list educations', function () {
    $edu = Education::create([
        'institution' => 'Elite Academy',
        'major' => 'Math',
        'start_date' => '2018-01-01',
        'order' => 0,
    ]);

    $response = $this->getJson('/api/v1/educations');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment([
            'institution' => 'Elite Academy',
            'major' => 'Math',
            'start_date' => '2018-01',
            'end_date' => null,
        ]);
});
