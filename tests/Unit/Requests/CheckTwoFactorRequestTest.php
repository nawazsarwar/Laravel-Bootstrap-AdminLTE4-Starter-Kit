<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\CheckTwoFactorRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

test('check two factor request authorizes when user has two factor code', function () {
    $user = User::factory()->create([
        'two_factor_code' => '123456',
        'two_factor_expires_at' => Carbon::now()->addMinutes(15)->format(config('panel.date_format') . ' ' . config('panel.time_format')),
    ]);
    $this->actingAs($user);
    
    $request = new CheckTwoFactorRequest();
    
    expect($request->authorize())->toBeTrue();
});

test('check two factor request denies when user has no two factor code', function () {
    $user = User::factory()->create(['two_factor_code' => null]);
    $this->actingAs($user);
    
    $request = new CheckTwoFactorRequest();
    
    expect(fn() => $request->authorize())->toThrow(\Symfony\Component\HttpKernel\Exception\HttpException::class);
});

test('check two factor request validates two factor code', function () {
    $rules = (new CheckTwoFactorRequest())->rules();
    
    expect($rules)->toHaveKey('two_factor_code');
    expect($rules['two_factor_code'])->toContain('required');
    expect($rules['two_factor_code'])->toContain('integer');
});

