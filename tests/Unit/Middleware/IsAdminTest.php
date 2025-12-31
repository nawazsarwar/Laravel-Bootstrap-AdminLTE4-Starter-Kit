<?php

namespace Tests\Unit\Middleware;

use App\Http\Middleware\IsAdmin;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

test('middleware allows admin user', function () {
    $adminRole = Role::firstOrCreate(['id' => 1], ['title' => 'Admin']);
    $user = User::factory()->create();
    $user->roles()->attach($adminRole);
    $this->actingAs($user);
    
    $middleware = new IsAdmin();
    $request = Request::create('/admin');
    
    $response = $middleware->handle($request, function ($req) {
        return response('OK');
    });
    
    expect($response->getContent())->toBe('OK');
});

test('middleware blocks non-admin user', function () {
    $role = Role::factory()->create(['id' => 2]);
    $user = User::factory()->create();
    $user->roles()->attach($role);
    $this->actingAs($user);
    
    $middleware = new IsAdmin();
    $request = Request::create('/admin');
    
    expect(fn() => $middleware->handle($request, function ($req) {
        return response('OK');
    }))->toThrow(\Symfony\Component\HttpKernel\Exception\HttpException::class);
});

test('middleware blocks unauthenticated user', function () {
    auth()->logout();
    
    $middleware = new IsAdmin();
    $request = Request::create('/admin');
    
    expect(fn() => $middleware->handle($request, function ($req) {
        return response('OK');
    }))->toThrow(\Symfony\Component\HttpKernel\Exception\HttpException::class);
});

