<?php

namespace Tests\Unit\Middleware;

use App\Http\Middleware\TwoFactorMiddleware;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

test('middleware allows access when user has no two factor code', function () {
    $user = User::factory()->create(['two_factor_code' => null]);
    $this->actingAs($user);
    
    $middleware = new TwoFactorMiddleware();
    $request = Request::create('/test');
    
    $response = $middleware->handle($request, function ($req) {
        return response('OK');
    });
    
    expect($response->getContent())->toBe('OK');
});

test('middleware redirects to two factor page when code exists', function () {
    $user = User::factory()->create([
        'two_factor_code' => '123456',
        'two_factor_expires_at' => Carbon::now()->addMinutes(15)->format(config('panel.date_format') . ' ' . config('panel.time_format')),
    ]);
    $this->actingAs($user);
    
    $middleware = new TwoFactorMiddleware();
    $request = Request::create('/test');
    
    $response = $middleware->handle($request, function ($req) {
        return response('OK');
    });
    
    expect($response->isRedirect())->toBeTrue();
    expect($response->getTargetUrl())->toContain('two-factor');
});

test('middleware allows access to two factor routes when code exists', function () {
    $user = User::factory()->create([
        'two_factor_code' => '123456',
        'two_factor_expires_at' => Carbon::now()->addMinutes(15)->format(config('panel.date_format') . ' ' . config('panel.time_format')),
    ]);
    $this->actingAs($user);
    
    $middleware = new TwoFactorMiddleware();
    $request = Request::create('/two-factor');
    
    $response = $middleware->handle($request, function ($req) {
        return response('OK');
    });
    
    expect($response->getContent())->toBe('OK');
});

test('middleware logs out user and redirects when code is expired', function () {
    $user = User::factory()->create([
        'two_factor_code' => '123456',
        'two_factor_expires_at' => Carbon::now()->subMinutes(20)->format(config('panel.date_format') . ' ' . config('panel.time_format')),
    ]);
    $this->actingAs($user);
    
    $middleware = new TwoFactorMiddleware();
    $request = Request::create('/test');
    
    $response = $middleware->handle($request, function ($req) {
        return response('OK');
    });
    
    expect($response->isRedirect())->toBeTrue();
    expect($response->getTargetUrl())->toContain('login');
    expect(auth()->check())->toBeFalse();
});

