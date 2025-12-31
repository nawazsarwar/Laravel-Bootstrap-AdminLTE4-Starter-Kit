<?php

namespace Tests\Unit\Middleware;

use App\Http\Middleware\VerificationMiddleware;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

test('middleware blocks unverified user', function () {
    $user = User::factory()->create(['verified' => false]);
    $this->actingAs($user);
    
    $middleware = new VerificationMiddleware();
    $request = Request::create('/test');
    
    $response = $middleware->handle($request, function ($req) {
        return response('OK');
    });
    
    expect($response->isRedirect())->toBeTrue();
    expect($response->getTargetUrl())->toContain('login');
    expect(auth()->check())->toBeFalse();
});

test('middleware allows verified user', function () {
    $user = User::factory()->create(['verified' => true]);
    $this->actingAs($user);
    
    $middleware = new VerificationMiddleware();
    $request = Request::create('/test');
    
    $response = $middleware->handle($request, function ($req) {
        return response('OK');
    });
    
    expect($response->getContent())->toBe('OK');
});

test('middleware handles unauthenticated users', function () {
    auth()->logout();
    
    $middleware = new VerificationMiddleware();
    $request = Request::create('/test');
    
    $response = $middleware->handle($request, function ($req) {
        return response('OK');
    });
    
    expect($response->getContent())->toBe('OK');
});

