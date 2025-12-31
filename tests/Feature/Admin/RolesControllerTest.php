<?php

namespace Tests\Feature\Admin;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

test('admin can view roles index', function () {
    $admin = $this->createAdminUser();
    $permission = Permission::firstOrCreate(['title' => 'role_access']);
    $admin->roles->first()->permissions()->attach($permission);
    $this->actingAs($admin);
    
    $response = $this->get(route('admin.roles.index'));
    
    $response->assertStatus(200);
    $response->assertViewIs('admin.roles.index');
});

test('admin can view create role form', function () {
    $admin = $this->createAdminUser();
    $permission = Permission::firstOrCreate(['title' => 'role_create']);
    $admin->roles->first()->permissions()->attach($permission);
    $this->actingAs($admin);
    
    $response = $this->get(route('admin.roles.create'));
    
    $response->assertStatus(200);
    $response->assertViewIs('admin.roles.create');
});

test('admin can create role', function () {
    $admin = $this->createAdminUser();
    $permission = Permission::firstOrCreate(['title' => 'role_create']);
    $admin->roles->first()->permissions()->attach($permission);
    $this->actingAs($admin);
    
    $permission1 = Permission::factory()->create();
    $permission2 = Permission::factory()->create();
    
    $response = $this->post(route('admin.roles.store'), [
        'title' => 'New Role',
        'permissions' => [$permission1->id, $permission2->id],
    ]);
    
    $response->assertRedirect(route('admin.roles.index'));
    $this->assertDatabaseHas('roles', [
        'title' => 'New Role',
    ]);
    
    $role = Role::where('title', 'New Role')->first();
    expect($role->permissions)->toHaveCount(2);
});

test('admin can view edit role form', function () {
    $admin = $this->createAdminUser();
    $permission = Permission::firstOrCreate(['title' => 'role_edit']);
    $admin->roles->first()->permissions()->attach($permission);
    $this->actingAs($admin);
    
    $role = Role::factory()->create();
    
    $response = $this->get(route('admin.roles.edit', $role));
    
    $response->assertStatus(200);
    $response->assertViewIs('admin.roles.edit');
});

test('admin can update role', function () {
    $admin = $this->createAdminUser();
    $permission = Permission::firstOrCreate(['title' => 'role_edit']);
    $admin->roles->first()->permissions()->attach($permission);
    $this->actingAs($admin);
    
    $role = Role::factory()->create();
    $permission1 = Permission::factory()->create();
    $permission2 = Permission::factory()->create();
    
    $response = $this->put(route('admin.roles.update', $role), [
        'title' => 'Updated Role',
        'permissions' => [$permission1->id, $permission2->id],
    ]);
    
    $response->assertRedirect(route('admin.roles.index'));
    $role->refresh();
    expect($role->title)->toBe('Updated Role');
    expect($role->permissions)->toHaveCount(2);
});

test('admin can view role', function () {
    $admin = $this->createAdminUser();
    $permission = Permission::firstOrCreate(['title' => 'role_show']);
    $admin->roles->first()->permissions()->attach($permission);
    $this->actingAs($admin);
    
    $role = Role::factory()->create();
    
    $response = $this->get(route('admin.roles.show', $role));
    
    $response->assertStatus(200);
    $response->assertViewIs('admin.roles.show');
});

test('admin can delete role', function () {
    $admin = $this->createAdminUser();
    $permission = Permission::firstOrCreate(['title' => 'role_delete']);
    $admin->roles->first()->permissions()->attach($permission);
    $this->actingAs($admin);
    
    $role = Role::factory()->create();
    
    $response = $this->delete(route('admin.roles.destroy', $role));
    
    $response->assertRedirect();
    $this->assertSoftDeleted('roles', ['id' => $role->id]);
});

test('admin can mass delete roles', function () {
    $admin = $this->createAdminUser();
    $permission = Permission::firstOrCreate(['title' => 'role_delete']);
    $admin->roles->first()->permissions()->attach($permission);
    $this->actingAs($admin);
    
    $roles = Role::factory()->count(3)->create();
    $ids = $roles->pluck('id')->toArray();
    
    $response = $this->delete(route('admin.roles.massDestroy'), [
        'ids' => $ids,
    ]);
    
    $response->assertStatus(204);
    foreach ($roles as $role) {
        $this->assertSoftDeleted('roles', ['id' => $role->id]);
    }
});

test('user without permission cannot access roles index', function () {
    $user = $this->actingAsUser();
    
    $response = $this->get(route('admin.roles.index'));
    
    $response->assertStatus(403);
});

