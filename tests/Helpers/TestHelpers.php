<?php

namespace Tests\Helpers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase;

trait TestHelpers
{
    /**
     * Ensure default roles exist for testing
     */
    protected function ensureDefaultRoles(): void
    {
        // Ensure admin role exists (only if it doesn't exist)
        if (!Role::find(1)) {
            Role::create(['id' => 1, 'title' => 'Admin']);
        }

        // Ensure default registration role exists (only if it doesn't exist)
        $defaultRoleId = (int)config('panel.registration_default_role', '2');
        if (!Role::find($defaultRoleId)) {
            Role::create(['id' => $defaultRoleId, 'title' => 'User']);
        }
    }

    /**
     * Create an admin user with all permissions
     */
    protected function createAdminUser(array $attributes = []): User
    {
        $this->ensureDefaultRoles();

        $user = User::factory()->create($attributes);

        // Get or create admin role (ID 1)
        $adminRole = Role::firstOrCreate(['id' => 1], ['title' => 'Admin']);

        // Detach any existing roles (from boot method) and attach admin role
        $user->roles()->detach();
        $user->roles()->attach($adminRole);

        return $user->fresh();
    }

    /**
     * Create a regular user
     */
    protected function createUser(array $attributes = []): User
    {
        $this->ensureDefaultRoles();
        return User::factory()->create($attributes);
    }

    /**
     * Create a role with permissions
     */
    protected function createRoleWithPermissions(string $roleTitle, array $permissionTitles = []): Role
    {
        $role = Role::factory()->create(['title' => $roleTitle]);

        $permissions = [];
        foreach ($permissionTitles as $permissionTitle) {
            $permissions[] = Permission::firstOrCreate(['title' => $permissionTitle]);
        }

        $role->permissions()->attach(collect($permissions)->pluck('id'));

        return $role->fresh();
    }

    /**
     * Create a user with specific role
     */
    protected function createUserWithRole(string $roleTitle, array $userAttributes = []): User
    {
        $this->ensureDefaultRoles();
        $role = Role::firstOrCreate(['title' => $roleTitle]);
        $user = User::factory()->create($userAttributes);

        // Detach any existing roles (from boot method) and attach specified role
        $user->roles()->detach();
        $user->roles()->attach($role);

        return $user->fresh();
    }

    /**
     * Create a permission
     */
    protected function createPermission(string $title): Permission
    {
        return Permission::firstOrCreate(['title' => $title]);
    }

    /**
     * Act as an admin user
     */
    protected function actingAsAdmin(): User
    {
        $user = $this->createAdminUser();
        $this->actingAs($user);

        return $user;
    }

    /**
     * Act as a regular user
     */
    protected function actingAsUser(array $attributes = []): User
    {
        $user = $this->createUser($attributes);
        $this->actingAs($user);

        return $user;
    }

    /**
     * Act as a user with specific role
     */
    protected function actingAsUserWithRole(string $roleTitle, array $userAttributes = []): User
    {
        $user = $this->createUserWithRole($roleTitle, $userAttributes);
        $this->actingAs($user);

        return $user;
    }
}

