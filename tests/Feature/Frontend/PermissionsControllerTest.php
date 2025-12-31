<?php

namespace Tests\Feature\Frontend;

use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

test('user can view permissions index', function () {
    $user = $this->createUserWithRole('User');
    $permission = Permission::firstOrCreate(['title' => 'permission_access']);
    $user->roles->first()->permissions()->attach($permission);
    $this->actingAs($user);
    
    $response = $this->get(route('frontend.permissions.index'));
    
    $response->assertStatus(200);
    $response->assertViewIs('frontend.permissions.index');
});

test('user can create permission', function () {
    $user = $this->createUserWithRole('User');
    $permission = Permission::firstOrCreate(['title' => 'permission_create']);
    $user->roles->first()->permissions()->attach($permission);
    $this->actingAs($user);
    
    $response = $this->post(route('frontend.permissions.store'), [
        'title' => 'new_permission',
    ]);
    
    $response->assertRedirect(route('frontend.permissions.index'));
    $this->assertDatabaseHas('permissions', [
        'title' => 'new_permission',
    ]);
});

test('user can update permission', function () {
    $user = $this->createUserWithRole('User');
    $permission = Permission::firstOrCreate(['title' => 'permission_edit']);
    $user->roles->first()->permissions()->attach($permission);
    $this->actingAs($user);
    
    $permission = Permission::factory()->create();
    
    $response = $this->put(route('frontend.permissions.update', $permission), [
        'title' => 'updated_permission',
    ]);
    
    $response->assertRedirect(route('frontend.permissions.index'));
    $permission->refresh();
    expect($permission->title)->toBe('updated_permission');
});

test('user can delete permission', function () {
    $user = $this->createUserWithRole('User');
    $permission = Permission::firstOrCreate(['title' => 'permission_delete']);
    $user->roles->first()->permissions()->attach($permission);
    $this->actingAs($user);
    
    $permission = Permission::factory()->create();
    
    $response = $this->delete(route('frontend.permissions.destroy', $permission));
    
    $response->assertRedirect();
    $this->assertSoftDeleted('permissions', ['id' => $permission->id]);
});

