<?php

use App\Models\OrganizationNode;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    Cache::flush();
});

test('it can create a root organization node', function () {
    $node = OrganizationNode::create([
        'title' => 'Executive Leadership',
        'names' => ['John Doe', 'Jane Smith'],
        'type' => 'leadership',
        'order' => 0,
        'is_active' => true,
    ]);

    $this->assertDatabaseHas('organization_nodes', [
        'title' => 'Executive Leadership',
        'parent_id' => null,
    ]);

    expect($node->names)->toBe(['John Doe', 'Jane Smith']);
});

test('it can create a child organization node', function () {
    $parent = OrganizationNode::create([
        'title' => 'Executive',
        'names' => ['CEO'],
        'type' => 'leadership',
        'order' => 0,
    ]);

    $child = OrganizationNode::create([
        'parent_id' => $parent->id,
        'title' => 'Engineering',
        'names' => ['CTO'],
        'type' => 'department',
        'order' => 0,
    ]);

    expect($child->parent_id)->toBe($parent->id);
    expect($parent->children->contains($child))->toBeTrue();
});

test('it returns full tree via api', function () {
    $root = OrganizationNode::create([
        'title' => 'Root',
        'names' => ['Admin'],
        'type' => 'leadership',
        'order' => 0,
        'is_active' => true,
    ]);

    $child = OrganizationNode::create([
        'parent_id' => $root->id,
        'title' => 'Child',
        'names' => ['Manager'],
        'type' => 'department',
        'order' => 0,
        'is_active' => true,
    ]);

    $response = $this->getJson('/api/organization');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'names',
                    'type',
                    'order',
                    'is_active',
                    'children',
                ],
            ],
            'cached_at',
        ]);

    $data = $response->json('data');
    expect($data)->toHaveCount(1);
    expect($data[0]['title'])->toBe('Root');
    expect($data[0]['children'])->toHaveCount(1);
    expect($data[0]['children'][0]['title'])->toBe('Child');
});

test('it only returns active nodes in api', function () {
    OrganizationNode::create([
        'title' => 'Active',
        'names' => ['Active User'],
        'type' => 'department',
        'order' => 0,
        'is_active' => true,
    ]);

    OrganizationNode::create([
        'title' => 'Inactive',
        'names' => ['Inactive User'],
        'type' => 'department',
        'order' => 1,
        'is_active' => false,
    ]);

    $response = $this->getJson('/api/organization');

    $data = $response->json('data');
    $titles = collect($data)->pluck('title')->toArray();

    expect($titles)->toContain('Active');
    expect($titles)->not->toContain('Inactive');
});

test('it caches the tree response', function () {
    OrganizationNode::create([
        'title' => 'Test',
        'names' => ['Test User'],
        'type' => 'department',
        'order' => 0,
        'is_active' => true,
    ]);

    $this->getJson('/api/organization');
    expect(Cache::has('organization_tree'))->toBeTrue();

    $response = $this->getJson('/api/organization');
    $response->assertStatus(200);
});

test('it invalidates cache on node save', function () {
    $node = OrganizationNode::create([
        'title' => 'Test',
        'names' => ['Test User'],
        'type' => 'department',
        'order' => 0,
        'is_active' => true,
    ]);

    $this->getJson('/api/organization');
    expect(Cache::has('organization_tree'))->toBeTrue();

    $node->update(['title' => 'Updated']);

    expect(Cache::has('organization_tree'))->toBeFalse();
});

test('it returns roots only via api', function () {
    $root1 = OrganizationNode::create([
        'title' => 'Root 1',
        'names' => ['User 1'],
        'type' => 'leadership',
        'order' => 0,
        'is_active' => true,
    ]);

    OrganizationNode::create([
        'title' => 'Root 2',
        'names' => ['User 2'],
        'type' => 'leadership',
        'order' => 1,
        'is_active' => true,
    ]);

    OrganizationNode::create([
        'parent_id' => $root1->id,
        'title' => 'Child',
        'names' => ['User 3'],
        'type' => 'department',
        'order' => 0,
        'is_active' => true,
    ]);

    $response = $this->getJson('/api/organization/roots');

    $response->assertStatus(200);
    $data = $response->json('data');

    expect($data)->toHaveCount(2);
    $titles = collect($data)->pluck('title')->toArray();
    expect($titles)->toContain('Root 1');
    expect($titles)->toContain('Root 2');
    expect($titles)->not->toContain('Child');
});

test('it returns specific node with children', function () {
    $parent = OrganizationNode::create([
        'title' => 'Parent',
        'names' => ['Parent User'],
        'type' => 'department',
        'order' => 0,
        'is_active' => true,
    ]);

    OrganizationNode::create([
        'parent_id' => $parent->id,
        'title' => 'Child',
        'names' => ['Child User'],
        'type' => 'team',
        'order' => 0,
        'is_active' => true,
    ]);

    $response = $this->getJson("/api/organization/{$parent->id}");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data' => [
                'id',
                'title',
                'names',
                'type',
                'order',
                'is_active',
                'children',
            ],
        ]);

    $data = $response->json('data');
    expect($data['title'])->toBe('Parent');
    expect($data['children'])->toHaveCount(1);
    expect($data['children'][0]['title'])->toBe('Child');
});

test('it requires authentication to clear cache', function () {
    $response = $this->postJson('/api/organization/clear-cache');
    $response->assertStatus(401);
});

test('authenticated user can clear cache', function () {
    $user = User::factory()->create();

    Cache::put('organization_tree', ['test'], 3600);

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/organization/clear-cache');

    $response->assertStatus(200)
        ->assertJson(['success' => true]);

    expect(Cache::has('organization_tree'))->toBeFalse();
});

test('it orders nodes correctly', function () {
    $parent = OrganizationNode::create([
        'title' => 'Parent',
        'names' => ['Parent'],
        'type' => 'leadership',
        'order' => 0,
    ]);

    OrganizationNode::create([
        'parent_id' => $parent->id,
        'title' => 'Third',
        'names' => ['Third'],
        'type' => 'department',
        'order' => 2,
    ]);

    OrganizationNode::create([
        'parent_id' => $parent->id,
        'title' => 'First',
        'names' => ['First'],
        'type' => 'department',
        'order' => 0,
    ]);

    OrganizationNode::create([
        'parent_id' => $parent->id,
        'title' => 'Second',
        'names' => ['Second'],
        'type' => 'department',
        'order' => 1,
    ]);

    $children = $parent->fresh()->children;
    $titles = $children->pluck('title')->toArray();

    expect($titles)->toBe(['First', 'Second', 'Third']);
});

test('it cascades delete to children', function () {
    $parent = OrganizationNode::create([
        'title' => 'Parent',
        'names' => ['Parent'],
        'type' => 'leadership',
        'order' => 0,
    ]);

    $child = OrganizationNode::create([
        'parent_id' => $parent->id,
        'title' => 'Child',
        'names' => ['Child'],
        'type' => 'department',
        'order' => 0,
    ]);

    OrganizationNode::create([
        'parent_id' => $child->id,
        'title' => 'Grandchild',
        'names' => ['Grandchild'],
        'type' => 'team',
        'order' => 0,
    ]);

    expect(OrganizationNode::count())->toBe(3);

    $parent->delete();

    expect(OrganizationNode::count())->toBe(0);
});

