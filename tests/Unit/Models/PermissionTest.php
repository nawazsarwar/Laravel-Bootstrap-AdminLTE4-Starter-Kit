<?php

namespace Tests\Unit\Models;

use App\Models\Permission;
use App\Models\Role;
use Tests\TestCase;

test('permission has roles relationship', function () {
    $permission = Permission::factory()->create();
    $role = Role::factory()->create();
    
    $role->permissions()->attach($permission);
    
    $permission->refresh();
    expect($permission->roles)->toHaveCount(1);
    expect($permission->roles->first()->id)->toBe($role->id);
});

test('permission uses soft deletes', function () {
    $permission = Permission::factory()->create();
    $permissionId = $permission->id;
    
    $permission->delete();
    
    expect(Permission::find($permissionId))->toBeNull();
    expect(Permission::withTrashed()->find($permissionId))->not->toBeNull();
});

