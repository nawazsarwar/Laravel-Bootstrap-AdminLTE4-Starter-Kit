<?php

namespace Tests\Feature\Frontend;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

test('user can view users index', function () {
    $user = $this->createUserWithRole('User');
    $permission = Permission::firstOrCreate(['title' => 'user_access']);
    $user->roles->first()->permissions()->attach($permission);
    $this->actingAs($user);
    
    $response = $this->get(route('frontend.users.index'));
    
    $response->assertStatus(200);
    $response->assertViewIs('frontend.users.index');
});

test('user can view create user form', function () {
    $user = $this->createUserWithRole('User');
    $permission = Permission::firstOrCreate(['title' => 'user_create']);
    $user->roles->first()->permissions()->attach($permission);
    $this->actingAs($user);
    
    $response = $this->get(route('frontend.users.create'));
    
    $response->assertStatus(200);
    $response->assertViewIs('frontend.users.create');
});

test('user can create user', function () {
    $user = $this->createUserWithRole('User');
    $permission = Permission::firstOrCreate(['title' => 'user_create']);
    $user->roles->first()->permissions()->attach($permission);
    $this->actingAs($user);
    
    $role = Role::factory()->create();
    
    $response = $this->post(route('frontend.users.store'), [
        'name' => 'New User',
        'email' => 'newuser@example.com',
        'password' => 'password123',
        'roles' => [$role->id],
    ]);
    
    $response->assertRedirect(route('frontend.users.index'));
    $this->assertDatabaseHas('users', [
        'email' => 'newuser@example.com',
    ]);
});

test('user can view edit user form', function () {
    $user = $this->createUserWithRole('User');
    $permission = Permission::firstOrCreate(['title' => 'user_edit']);
    $user->roles->first()->permissions()->attach($permission);
    $this->actingAs($user);
    
    $targetUser = User::factory()->create();
    
    $response = $this->get(route('frontend.users.edit', $targetUser));
    
    $response->assertStatus(200);
    $response->assertViewIs('frontend.users.edit');
});

test('user can update user', function () {
    $user = $this->createUserWithRole('User');
    $permission = Permission::firstOrCreate(['title' => 'user_edit']);
    $user->roles->first()->permissions()->attach($permission);
    $this->actingAs($user);
    
    $targetUser = User::factory()->create();
    $role = Role::factory()->create();
    
    $response = $this->put(route('frontend.users.update', $targetUser), [
        'name' => 'Updated Name',
        'email' => $targetUser->email,
        'roles' => [$role->id],
    ]);
    
    $response->assertRedirect(route('frontend.users.index'));
    $targetUser->refresh();
    expect($targetUser->name)->toBe('Updated Name');
});

test('user can view user', function () {
    $user = $this->createUserWithRole('User');
    $permission = Permission::firstOrCreate(['title' => 'user_show']);
    $user->roles->first()->permissions()->attach($permission);
    $this->actingAs($user);
    
    $targetUser = User::factory()->create();
    
    $response = $this->get(route('frontend.users.show', $targetUser));
    
    $response->assertStatus(200);
    $response->assertViewIs('frontend.users.show');
});

test('user can delete user', function () {
    $user = $this->createUserWithRole('User');
    $permission = Permission::firstOrCreate(['title' => 'user_delete']);
    $user->roles->first()->permissions()->attach($permission);
    $this->actingAs($user);
    
    $targetUser = User::factory()->create();
    
    $response = $this->delete(route('frontend.users.destroy', $targetUser));
    
    $response->assertRedirect();
    $this->assertSoftDeleted('users', ['id' => $targetUser->id]);
});

test('user can mass delete users', function () {
    $user = $this->createUserWithRole('User');
    $permission = Permission::firstOrCreate(['title' => 'user_delete']);
    $user->roles->first()->permissions()->attach($permission);
    $this->actingAs($user);
    
    $users = User::factory()->count(3)->create();
    $ids = $users->pluck('id')->toArray();
    
    $response = $this->delete(route('frontend.users.massDestroy'), [
        'ids' => $ids,
    ]);
    
    $response->assertStatus(204);
});

