<?php

namespace Tests\Unit\Models;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Tests\TestCase;

test('role has permissions relationship', function () {
    $role = Role::factory()->create();
    $permission = Permission::factory()->create();
    
    $role->permissions()->attach($permission);
    
    expect($role->permissions)->toHaveCount(1);
    expect($role->permissions->first()->id)->toBe($permission->id);
});

test('role has users relationship', function () {
    $role = Role::factory()->create();
    $user = User::factory()->create();
    
    // Detach default role first
    $user->roles()->detach();
    $user->roles()->attach($role);
    
    $role->refresh();
    expect($role->users)->toHaveCount(1);
    expect($role->users->first()->id)->toBe($user->id);
});

test('role uses soft deletes', function () {
    $role = Role::factory()->create();
    $roleId = $role->id;
    
    $role->delete();
    
    expect(Role::find($roleId))->toBeNull();
    expect(Role::withTrashed()->find($roleId))->not->toBeNull();
});

test('role can sync permissions', function () {
    $role = Role::factory()->create();
    $permission1 = Permission::factory()->create();
    $permission2 = Permission::factory()->create();
    
    $role->permissions()->sync([$permission1->id, $permission2->id]);
    
    expect($role->permissions)->toHaveCount(2);
});

