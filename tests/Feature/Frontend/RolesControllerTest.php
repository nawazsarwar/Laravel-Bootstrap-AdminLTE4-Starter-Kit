<?php

namespace Tests\Feature\Frontend;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

test('user can view roles index', function () {
    $user = $this->createUserWithRole('User');
    $permission = Permission::firstOrCreate(['title' => 'role_access']);
    $user->roles->first()->permissions()->attach($permission);
    $this->actingAs($user);
    
    $response = $this->get(route('frontend.roles.index'));
    
    $response->assertStatus(200);
    $response->assertViewIs('frontend.roles.index');
});

test('user can create role', function () {
    $user = $this->createUserWithRole('User');
    $permission = Permission::firstOrCreate(['title' => 'role_create']);
    $user->roles->first()->permissions()->attach($permission);
    $this->actingAs($user);
    
    $permission1 = Permission::factory()->create();
    
    $response = $this->post(route('frontend.roles.store'), [
        'title' => 'New Role',
        'permissions' => [$permission1->id],
    ]);
    
    $response->assertRedirect(route('frontend.roles.index'));
    $this->assertDatabaseHas('roles', [
        'title' => 'New Role',
    ]);
});

test('user can update role', function () {
    $user = $this->createUserWithRole('User');
    $permission = Permission::firstOrCreate(['title' => 'role_edit']);
    $user->roles->first()->permissions()->attach($permission);
    $this->actingAs($user);
    
    $role = Role::factory()->create();
    $permission1 = Permission::factory()->create();
    
    $response = $this->put(route('frontend.roles.update', $role), [
        'title' => 'Updated Role',
        'permissions' => [$permission1->id],
    ]);
    
    $response->assertRedirect(route('frontend.roles.index'));
    $role->refresh();
    expect($role->title)->toBe('Updated Role');
});

test('user can delete role', function () {
    $user = $this->createUserWithRole('User');
    $permission = Permission::firstOrCreate(['title' => 'role_delete']);
    $user->roles->first()->permissions()->attach($permission);
    $this->actingAs($user);
    
    $role = Role::factory()->create();
    
    $response = $this->delete(route('frontend.roles.destroy', $role));
    
    $response->assertRedirect();
    $this->assertSoftDeleted('roles', ['id' => $role->id]);
});

