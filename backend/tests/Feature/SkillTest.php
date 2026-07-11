<?php

use App\Models\Skill;
use App\Models\SkillItem;
use App\Models\Technology;
use App\Models\User;

test('guests cannot access skills index', function () {
    $response = $this->get('/admin-cms/skills');

    $response->assertStatus(302);
    $response->assertRedirect(route('login'));
});

test('guests cannot store skills groups', function () {
    $response = $this->post('/admin-cms/skills', [
        'name' => 'Backend Development',
        'order' => 0,
    ]);

    $response->assertStatus(302);
    $response->assertRedirect(route('login'));
});

test('guests cannot store skills items', function () {
    $response = $this->post('/admin-cms/skill-items', [
        'skill_id' => 1,
        'name' => 'Problem Solving',
        'level' => 'expert',
        'is_featured' => false,
        'order' => 0,
    ]);

    $response->assertStatus(302);
    $response->assertRedirect(route('login'));
});

test('administrator can view skills index', function () {
    $user = User::factory()->create();
    $group = Skill::create(['name' => 'Languages', 'order' => 0]);

    SkillItem::create([
        'skill_id' => $group->id,
        'name' => 'English',
        'level' => 'expert',
        'years_of_experience' => 5.0,
        'is_featured' => true,
        'order' => 1,
    ]);

    $response = $this->actingAs($user)
        ->get('/admin-cms/skills');

    $response->assertStatus(200);
});

test('administrator can create a skill group', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->post('/admin-cms/skills', [
            'name' => 'Backend Development',
            'order' => 1,
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('skills', [
        'name' => 'Backend Development',
        'order' => 1,
    ]);
});

test('administrator can create a skill item linked to technology', function () {
    $user = User::factory()->create();
    $group = Skill::create(['name' => 'Backend', 'order' => 0]);
    $tech = Technology::create([
        'name' => 'Laravel',
        'slug' => 'laravel',
        'category' => 'backend',
    ]);

    $response = $this->actingAs($user)
        ->post('/admin-cms/skill-items', [
            'skill_id' => $group->id,
            'technology_id' => $tech->id,
            'level' => 'expert',
            'years_of_experience' => 3.5,
            'is_featured' => true,
            'order' => 0,
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('skill_items', [
        'skill_id' => $group->id,
        'technology_id' => $tech->id,
        'level' => 'expert',
        'years_of_experience' => 3.5,
        'is_featured' => true,
    ]);
});

test('administrator can create a custom skill item', function () {
    $user = User::factory()->create();
    $group = Skill::create(['name' => 'Soft Skills', 'order' => 0]);

    $response = $this->actingAs($user)
        ->post('/admin-cms/skill-items', [
            'skill_id' => $group->id,
            'name' => 'Problem Solving',
            'level' => 'expert',
            'years_of_experience' => null,
            'is_featured' => false,
            'order' => 1,
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('skill_items', [
        'skill_id' => $group->id,
        'name' => 'Problem Solving',
        'technology_id' => null,
        'level' => 'expert',
    ]);
});

test('administrator cannot create a skill item without name or technology_id', function () {
    $user = User::factory()->create();
    $group = Skill::create(['name' => 'Soft Skills', 'order' => 0]);

    $response = $this->actingAs($user)
        ->post('/admin-cms/skill-items', [
            'skill_id' => $group->id,
            'level' => 'expert',
            'is_featured' => false,
            'order' => 1,
        ]);

    $response->assertStatus(302);
    $response->assertSessionHasErrors(['name', 'technology_id']);
});

test('administrator cannot link same technology to multiple skill items', function () {
    $user = User::factory()->create();
    $group = Skill::create(['name' => 'Backend', 'order' => 0]);
    $tech = Technology::create(['name' => 'PHP', 'slug' => 'php', 'category' => 'backend']);

    SkillItem::create([
        'skill_id' => $group->id,
        'technology_id' => $tech->id,
        'level' => 'expert',
    ]);

    $response = $this->actingAs($user)
        ->post('/admin-cms/skill-items', [
            'skill_id' => $group->id,
            'technology_id' => $tech->id,
            'level' => 'beginner',
            'is_featured' => false,
            'order' => 0,
        ]);

    $response->assertStatus(302);
    $response->assertSessionHasErrors(['technology_id']);
});

test('administrator can update a skill group', function () {
    $user = User::factory()->create();
    $group = Skill::create(['name' => 'DevOps Stack', 'order' => 0]);

    $response = $this->actingAs($user)
        ->put("/admin-cms/skills/{$group->id}", [
            'name' => 'Cloud & DevOps',
            'order' => 2,
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('skills', [
        'id' => $group->id,
        'name' => 'Cloud & DevOps',
        'order' => 2,
    ]);
});

test('administrator can update a skill item', function () {
    $user = User::factory()->create();
    $group = Skill::create(['name' => 'General', 'order' => 0]);
    $item = SkillItem::create([
        'skill_id' => $group->id,
        'name' => 'Critical Thinking',
        'level' => 'intermediate',
        'is_featured' => false,
        'order' => 0,
    ]);

    $response = $this->actingAs($user)
        ->put("/admin-cms/skill-items/{$item->id}", [
            'skill_id' => $group->id,
            'name' => 'Advanced Critical Thinking',
            'level' => 'expert',
            'is_featured' => true,
            'order' => 1,
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('skill_items', [
        'id' => $item->id,
        'name' => 'Advanced Critical Thinking',
        'level' => 'expert',
        'is_featured' => true,
        'order' => 1,
    ]);
});

test('administrator can delete a skill group and cascade deletes items', function () {
    $user = User::factory()->create();
    $group = Skill::create(['name' => 'Backend', 'order' => 0]);
    $item = SkillItem::create(['skill_id' => $group->id, 'name' => 'PHP', 'level' => 'expert']);

    $response = $this->actingAs($user)
        ->delete("/admin-cms/skills/{$group->id}");

    $response->assertRedirect();
    $this->assertDatabaseMissing('skills', ['id' => $group->id]);
    $this->assertDatabaseMissing('skill_items', ['id' => $item->id]);
});

test('administrator can delete a skill item', function () {
    $user = User::factory()->create();
    $group = Skill::create(['name' => 'Backend', 'order' => 0]);
    $item = SkillItem::create(['skill_id' => $group->id, 'name' => 'PHP', 'level' => 'expert']);

    $response = $this->actingAs($user)
        ->delete("/admin-cms/skill-items/{$item->id}");

    $response->assertRedirect();
    $this->assertDatabaseMissing('skill_items', ['id' => $item->id]);
});

test('administrator can reorder groups', function () {
    $user = User::factory()->create();
    $group1 = Skill::create(['name' => 'Backend', 'order' => 0]);
    $group2 = Skill::create(['name' => 'Frontend', 'order' => 1]);

    $response = $this->actingAs($user)
        ->post('/admin-cms/skills/reorder', [
            'ids' => [$group2->id, $group1->id],
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('skills', ['id' => $group2->id, 'order' => 0]);
    $this->assertDatabaseHas('skills', ['id' => $group1->id, 'order' => 1]);
});

test('administrator can reorder items within a group', function () {
    $user = User::factory()->create();
    $group = Skill::create(['name' => 'Soft Skills', 'order' => 0]);
    $item1 = SkillItem::create(['skill_id' => $group->id, 'name' => 'Communication', 'level' => 'advanced', 'order' => 0]);
    $item2 = SkillItem::create(['skill_id' => $group->id, 'name' => 'Leadership', 'level' => 'advanced', 'order' => 1]);

    $response = $this->actingAs($user)
        ->post('/admin-cms/skill-items/reorder', [
            'ids' => [$item2->id, $item1->id],
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('skill_items', ['id' => $item2->id, 'order' => 0]);
    $this->assertDatabaseHas('skill_items', ['id' => $item1->id, 'order' => 1]);
});

test('public api can list hierarchical skills with items', function () {
    $tech = Technology::create([
        'name' => 'Nuxt',
        'slug' => 'nuxt',
        'category' => 'frontend',
    ]);

    $group1 = Skill::create(['name' => 'Technical Skills', 'order' => 0]);
    $group2 = Skill::create(['name' => 'Languages', 'order' => 1]);

    $item1 = SkillItem::create([
        'skill_id' => $group1->id,
        'technology_id' => $tech->id,
        'level' => 'expert',
        'years_of_experience' => 4.0,
        'is_featured' => true,
        'order' => 0,
    ]);

    $item2 = SkillItem::create([
        'skill_id' => $group2->id,
        'name' => 'German',
        'level' => 'beginner',
        'is_featured' => false,
        'order' => 0,
    ]);

    // Index test
    $response = $this->getJson('/api/v1/skills');
    $response->assertStatus(200)
        ->assertJsonCount(2, 'data')
        ->assertJsonFragment([
            'id' => $group1->id,
            'name' => 'Technical Skills',
        ])
        ->assertJsonFragment([
            'id' => $group2->id,
            'name' => 'Languages',
        ]);

    // Check nested structure
    $data = $response->json('data');
    expect($data[0]['items'])->toHaveCount(1);
    expect($data[0]['items'][0]['id'])->toBe($item1->id);
    expect($data[0]['items'][0]['display_name'])->toBe('Nuxt');
    expect($data[0]['items'][0]['level'])->toBe('expert');

    expect($data[1]['items'])->toHaveCount(1);
    expect($data[1]['items'][0]['id'])->toBe($item2->id);
    expect($data[1]['items'][0]['display_name'])->toBe('German');
    expect($data[1]['items'][0]['level'])->toBe('beginner');

    // Featured API filter test
    $responseFeatured = $this->getJson('/api/v1/skills?featured=1');
    $responseFeatured->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment([
            'id' => $group1->id,
        ])
        ->assertJsonMissing([
            'id' => $group2->id,
        ]);
});
