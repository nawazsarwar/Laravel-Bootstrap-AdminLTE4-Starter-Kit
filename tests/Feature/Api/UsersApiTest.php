<?php

namespace Tests\Feature\Api;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

test('authenticated user can get users list', function () {
    $admin = $this->createAdminUser();
    $permission = Permission::firstOrCreate(['title' => 'user_access']);
    $admin->roles->first()->permissions()->attach($permission);
    Sanctum::actingAs($admin);
    
    User::factory()->count(3)->create();
    
    $response = $this->getJson('/api/v1/users');
    
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'data' => [
            '*' => ['id', 'name', 'email'],
        ],
    ]);
});

test('unauthenticated user cannot access users api', function () {
    $response = $this->getJson('/api/v1/users');
    
    $response->assertStatus(401);
});

test('authenticated user can create user', function () {
    $admin = $this->createAdminUser();
    $permission = Permission::firstOrCreate(['title' => 'user_create']);
    $admin->roles->first()->permissions()->attach($permission);
    Sanctum::actingAs($admin);
    
    $role = Role::factory()->create();
    
    $response = $this->postJson('/api/v1/users', [
        'name' => 'API User',
        'email' => 'api@example.com',
        'password' => 'password123',
        'roles' => [$role->id],
    ]);
    
    $response->assertStatus(201);
    $this->assertDatabaseHas('users', [
        'email' => 'api@example.com',
    ]);
});

test('authenticated user can view user', function () {
    $admin = $this->createAdminUser();
    $permission = Permission::firstOrCreate(['title' => 'user_show']);
    $admin->roles->first()->permissions()->attach($permission);
    Sanctum::actingAs($admin);
    
    $user = User::factory()->create();
    
    $response = $this->getJson("/api/v1/users/{$user->id}");
    
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'data' => ['id', 'name', 'email'],
    ]);
});

test('authenticated user can update user', function () {
    $admin = $this->createAdminUser();
    $permission = Permission::firstOrCreate(['title' => 'user_edit']);
    $admin->roles->first()->permissions()->attach($permission);
    Sanctum::actingAs($admin);
    
    $user = User::factory()->create();
    $role = Role::factory()->create();
    
    $response = $this->putJson("/api/v1/users/{$user->id}", [
        'name' => 'Updated Name',
        'email' => $user->email,
        'roles' => [$role->id],
    ]);
    
    $response->assertStatus(202);
    $user->refresh();
    expect($user->name)->toBe('Updated Name');
});

test('authenticated user can delete user', function () {
    $admin = $this->createAdminUser();
    $permission = Permission::firstOrCreate(['title' => 'user_delete']);
    $admin->roles->first()->permissions()->attach($permission);
    Sanctum::actingAs($admin);
    
    $user = User::factory()->create();
    
    $response = $this->deleteJson("/api/v1/users/{$user->id}");
    
    $response->assertStatus(204);
    $this->assertSoftDeleted('users', ['id' => $user->id]);
});

test('user without permission cannot access users api', function () {
    $user = $this->createUser();
    Sanctum::actingAs($user);
    
    $response = $this->getJson('/api/v1/users');
    
    $response->assertStatus(403);
});

