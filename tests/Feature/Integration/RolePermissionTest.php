<?php

namespace Tests\Feature\Integration;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

test('complete role permission assignment flow works', function () {
    // Step 1: Create permissions
    $permission1 = Permission::factory()->create(['title' => 'user_access']);
    $permission2 = Permission::factory()->create(['title' => 'user_create']);
    
    // Step 2: Create role and assign permissions
    $role = Role::factory()->create(['title' => 'Editor']);
    $role->permissions()->attach([$permission1->id, $permission2->id]);
    
    expect($role->permissions)->toHaveCount(2);
    
    // Step 3: Create user and assign role
    $user = User::factory()->create();
    $user->roles()->attach($role);
    
    expect($user->roles)->toHaveCount(1);
    expect($user->roles->first()->id)->toBe($role->id);
    
    // Step 4: Test that gates are defined (via middleware)
    $this->actingAs($user);
    
    // Simulate AuthGates middleware
    $roles = Role::with('permissions')->get();
    $permissionsArray = [];
    
    foreach ($roles as $r) {
        foreach ($r->permissions as $perm) {
            $permissionsArray[$perm->title][] = $r->id;
        }
    }
    
    foreach ($permissionsArray as $title => $roleIds) {
        Gate::define($title, function ($u) use ($roleIds) {
            return count(array_intersect($u->roles->pluck('id')->toArray(), $roleIds)) > 0;
        });
    }
    
    // Step 5: Verify user has access to permissions
    expect(Gate::allows('user_access'))->toBeTrue();
    expect(Gate::allows('user_create'))->toBeTrue();
    
    // Step 6: Update role permissions
    $permission3 = Permission::factory()->create(['title' => 'user_delete']);
    $role->permissions()->sync([$permission1->id, $permission3->id]);
    
    $role->refresh();
    expect($role->permissions)->toHaveCount(2);
    expect($role->permissions->pluck('id')->contains($permission3->id))->toBeTrue();
});

