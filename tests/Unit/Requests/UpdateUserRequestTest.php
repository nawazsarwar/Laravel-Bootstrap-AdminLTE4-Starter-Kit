<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\UpdateUserRequest;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

test('update user request authorizes user with permission', function () {
    $admin = $this->createAdminUser();
    $permission = Permission::firstOrCreate(['title' => 'user_edit']);
    $admin->roles->first()->permissions()->attach($permission);
    $this->actingAs($admin);
    
    $user = User::factory()->create();
    $httpRequest = \Illuminate\Http\Request::create("/admin/users/{$user->id}", 'PUT');
    $request = UpdateUserRequest::createFrom($httpRequest);
    $request->setContainer(app());
    $request->setRedirector(app('redirect'));
    $request->setRouteResolver(function () use ($user) {
        $route = \Illuminate\Routing\Route::put('/admin/users/{user}', function () {});
        $route->setParameter('user', $user);
        return $route;
    });
    
    expect($request->authorize())->toBeTrue();
});

test('update user request validates email is unique except current user', function () {
    $user = User::factory()->create(['email' => 'test@example.com']);
    $httpRequest = \Illuminate\Http\Request::create("/admin/users/{$user->id}", 'PUT');
    $request = UpdateUserRequest::createFrom($httpRequest);
    $request->setRouteResolver(function () use ($user) {
        $route = \Illuminate\Routing\Route::put('/admin/users/{user}', function () {});
        $route->setParameter('user', $user);
        return $route;
    });
    $rules = $request->rules();
    
    expect($rules['email'])->toContain('unique:users');
});

test('update user request validates required fields', function () {
    $user = User::factory()->create();
    $httpRequest = \Illuminate\Http\Request::create("/admin/users/{$user->id}", 'PUT');
    $request = UpdateUserRequest::createFrom($httpRequest);
    $request->setRouteResolver(function () use ($user) {
        $route = \Illuminate\Routing\Route::put('/admin/users/{user}', function () {});
        $route->setParameter('user', $user);
        return $route;
    });
    $rules = $request->rules();
    
    expect($rules)->toHaveKey('name');
    expect($rules)->toHaveKey('email');
    expect($rules)->toHaveKey('roles');
    
    expect($rules['name'])->toContain('required');
    expect($rules['email'])->toContain('required');
    expect($rules['roles'])->toContain('required');
});

