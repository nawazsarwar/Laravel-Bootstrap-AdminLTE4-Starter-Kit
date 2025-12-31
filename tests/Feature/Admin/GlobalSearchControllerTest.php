<?php

namespace Tests\Feature\Admin;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

test('admin can search users', function () {
    $admin = $this->createAdminUser();
    $this->actingAs($admin);
    
    User::factory()->create(['name' => 'John Doe', 'email' => 'john@example.com']);
    User::factory()->create(['name' => 'Jane Smith', 'email' => 'jane@example.com']);
    
    $response = $this->getJson(route('admin.globalSearch', ['search' => ['term' => 'John']]));
    
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'results' => [
            '*' => ['name', 'email', 'model', 'url'],
        ],
    ]);
    
    $results = $response->json('results');
    expect(count($results))->toBeGreaterThan(0);
});

test('global search returns json response', function () {
    $admin = $this->createAdminUser();
    $this->actingAs($admin);
    
    $response = $this->getJson(route('admin.globalSearch', ['search' => ['term' => 'test']]));
    
    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'application/json');
});

test('global search handles invalid search term', function () {
    $admin = $this->createAdminUser();
    $this->actingAs($admin);
    
    $response = $this->getJson(route('admin.globalSearch'), [
        'search' => [],
    ]);
    
    $response->assertStatus(400);
});

test('global search limits results to 10', function () {
    $admin = $this->createAdminUser();
    $this->actingAs($admin);
    
    User::factory()->count(15)->create(['name' => 'Test User']);
    
    $response = $this->getJson(route('admin.globalSearch', ['search' => ['term' => 'Test']]));
    
    $response->assertStatus(200);
    $results = $response->json('results');
    if ($results) {
        expect(count($results))->toBeLessThanOrEqual(10);
    }
});

