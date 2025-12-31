<?php

namespace Tests\Unit\Middleware;

use App\Http\Middleware\AuthGates;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

test('middleware defines gates based on roles and permissions', function () {
    $permission = Permission::factory()->create(['title' => 'user_access']);
    $role = Role::factory()->create();
    $role->permissions()->attach($permission);
    
    $user = User::factory()->create();
    $user->roles()->attach($role);
    $this->actingAs($user);
    
    $middleware = new AuthGates();
    $request = Request::create('/test');
    
    $middleware->handle($request, function ($req) {
        return response('OK');
    });
    
    expect(Gate::allows('user_access'))->toBeTrue();
});

test('middleware caches permissions', function () {
    Cache::flush();
    
    $permission = Permission::factory()->create(['title' => 'user_access']);
    $role = Role::factory()->create();
    $role->permissions()->attach($permission);
    
    $user = User::factory()->create();
    $user->roles()->attach($role);
    $this->actingAs($user);
    
    $middleware = new AuthGates();
    $request = Request::create('/test');
    
    $middleware->handle($request, function ($req) {
        return response('OK');
    });
    
    expect(Cache::has('auth_gates_permissions'))->toBeTrue();
});

test('middleware handles unauthenticated users', function () {
    auth()->logout();
    
    $middleware = new AuthGates();
    $request = Request::create('/test');
    
    $response = $middleware->handle($request, function ($req) {
        return response('OK');
    });
    
    expect($response->getContent())->toBe('OK');
});

