<?php

namespace Tests\Feature\Admin;

use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

test('admin can view permissions index', function () {
    $admin = $this->createAdminUser();
    $permission = Permission::firstOrCreate(['title' => 'permission_access']);
    $admin->roles->first()->permissions()->attach($permission);
    $this->actingAs($admin);
    
    $response = $this->get(route('admin.permissions.index'));
    
    $response->assertStatus(200);
    $response->assertViewIs('admin.permissions.index');
});

test('admin can view create permission form', function () {
    $admin = $this->createAdminUser();
    $permission = Permission::firstOrCreate(['title' => 'permission_create']);
    $admin->roles->first()->permissions()->attach($permission);
    $this->actingAs($admin);
    
    $response = $this->get(route('admin.permissions.create'));
    
    $response->assertStatus(200);
    $response->assertViewIs('admin.permissions.create');
});

test('admin can create permission', function () {
    $admin = $this->createAdminUser();
    $permission = Permission::firstOrCreate(['title' => 'permission_create']);
    $admin->roles->first()->permissions()->attach($permission);
    $this->actingAs($admin);
    
    $response = $this->post(route('admin.permissions.store'), [
        'title' => 'new_permission',
    ]);
    
    $response->assertRedirect(route('admin.permissions.index'));
    $this->assertDatabaseHas('permissions', [
        'title' => 'new_permission',
    ]);
});

test('admin can view edit permission form', function () {
    $admin = $this->createAdminUser();
    $permission = Permission::firstOrCreate(['title' => 'permission_edit']);
    $admin->roles->first()->permissions()->attach($permission);
    $this->actingAs($admin);
    
    $permission = Permission::factory()->create();
    
    $response = $this->get(route('admin.permissions.edit', $permission));
    
    $response->assertStatus(200);
    $response->assertViewIs('admin.permissions.edit');
});

test('admin can update permission', function () {
    $admin = $this->createAdminUser();
    $permission = Permission::firstOrCreate(['title' => 'permission_edit']);
    $admin->roles->first()->permissions()->attach($permission);
    $this->actingAs($admin);
    
    $permission = Permission::factory()->create();
    
    $response = $this->put(route('admin.permissions.update', $permission), [
        'title' => 'updated_permission',
    ]);
    
    $response->assertRedirect(route('admin.permissions.index'));
    $permission->refresh();
    expect($permission->title)->toBe('updated_permission');
});

test('admin can view permission', function () {
    $admin = $this->createAdminUser();
    $permission = Permission::firstOrCreate(['title' => 'permission_show']);
    $admin->roles->first()->permissions()->attach($permission);
    $this->actingAs($admin);
    
    $permission = Permission::factory()->create();
    
    $response = $this->get(route('admin.permissions.show', $permission));
    
    $response->assertStatus(200);
    $response->assertViewIs('admin.permissions.show');
});

test('admin can delete permission', function () {
    $admin = $this->createAdminUser();
    $permission = Permission::firstOrCreate(['title' => 'permission_delete']);
    $admin->roles->first()->permissions()->attach($permission);
    $this->actingAs($admin);
    
    $permission = Permission::factory()->create();
    
    $response = $this->delete(route('admin.permissions.destroy', $permission));
    
    $response->assertRedirect();
    $this->assertSoftDeleted('permissions', ['id' => $permission->id]);
});

test('admin can mass delete permissions', function () {
    $admin = $this->createAdminUser();
    $permission = Permission::firstOrCreate(['title' => 'permission_delete']);
    $admin->roles->first()->permissions()->attach($permission);
    $this->actingAs($admin);
    
    $permissions = Permission::factory()->count(3)->create();
    $ids = $permissions->pluck('id')->toArray();
    
    $response = $this->delete(route('admin.permissions.massDestroy'), [
        'ids' => $ids,
    ]);
    
    $response->assertStatus(204);
    foreach ($permissions as $permission) {
        $this->assertSoftDeleted('permissions', ['id' => $permission->id]);
    }
});

test('user without permission cannot access permissions index', function () {
    $user = $this->actingAsUser();
    
    $response = $this->get(route('admin.permissions.index'));
    
    $response->assertStatus(403);
});

