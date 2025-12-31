<?php

namespace Tests\Unit\Models;

use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

test('user has roles relationship', function () {
    $user = User::factory()->create();
    $role = Role::factory()->create();
    
    // Detach default role first (attached by boot method)
    $user->roles()->detach();
    $user->roles()->attach($role);
    
    $user->refresh();
    expect($user->roles)->toHaveCount(1);
    expect($user->roles->first()->id)->toBe($role->id);
});

test('user can generate two factor code', function () {
    $user = User::factory()->create(['two_factor' => true]);
    
    $user->generateTwoFactorCode();
    
    expect($user->two_factor_code)->not->toBeNull();
    expect($user->two_factor_code)->toBeNumeric();
    expect(strlen((string)$user->two_factor_code))->toBe(6);
    expect($user->two_factor_expires_at)->not->toBeNull();
});

test('user can reset two factor code', function () {
    $user = User::factory()->create([
        'two_factor_code' => '123456',
        'two_factor_expires_at' => now()->addMinutes(15),
    ]);
    
    $user->resetTwoFactorCode();
    
    expect($user->two_factor_code)->toBeNull();
    expect($user->two_factor_expires_at)->toBeNull();
});

test('user password is hashed when set', function () {
    $user = User::factory()->create(['password' => 'plaintext']);
    
    expect(Hash::check('plaintext', $user->password))->toBeTrue();
    expect($user->password)->not->toBe('plaintext');
});

test('user is_admin attribute returns true for admin role', function () {
    $user = User::factory()->create();
    $adminRole = Role::firstOrCreate(['id' => 1], ['title' => 'Admin']);
    $user->roles()->attach($adminRole);
    
    expect($user->is_admin)->toBeTrue();
});

test('user is_admin attribute returns false for non-admin', function () {
    $user = User::factory()->create();
    // Detach default role first
    $user->roles()->detach();
    $role = Role::factory()->create();
    $user->roles()->attach($role);
    
    $user->refresh();
    expect($user->is_admin)->toBeFalse();
});

test('user creates verification token on creation when not authenticated', function () {
    auth()->logout();
    
    $user = User::factory()->create(['verified' => false]);
    
    expect($user->verification_token)->not->toBeNull();
    expect(Str::length($user->verification_token))->toBe(64);
});

test('user is auto verified when created by authenticated user', function () {
    $admin = User::factory()->create();
    $this->actingAs($admin);
    
    $user = User::factory()->create();
    
    expect($user->verified)->toBe(1);
    expect($user->verified_at)->not->toBeNull();
});

test('user assigns default role on registration', function () {
    auth()->logout();
    
    $defaultRoleId = config('panel.registration_default_role', '2');
    $defaultRole = Role::firstOrCreate(['id' => $defaultRoleId], ['title' => 'User']);
    
    $user = User::factory()->create(['verified' => false]);
    
    expect($user->roles)->toHaveCount(1);
    expect($user->roles->first()->id)->toBe((int)$defaultRoleId);
});

test('user email verified at accessor formats date correctly', function () {
    $date = Carbon::now();
    $user = User::factory()->create(['email_verified_at' => $date]);
    
    $formatted = $user->email_verified_at;
    $expected = $date->format(config('panel.date_format') . ' ' . config('panel.time_format'));
    
    expect($formatted)->toBe($expected);
});

test('user verified at accessor formats date correctly', function () {
    $date = Carbon::now();
    $user = User::factory()->create([
        'verified' => true,
        'verified_at' => $date,
    ]);
    
    $formatted = $user->verified_at;
    $expected = $date->format(config('panel.date_format') . ' ' . config('panel.time_format'));
    
    expect($formatted)->toBe($expected);
});

test('user two factor expires at accessor formats date correctly', function () {
    $date = Carbon::now()->addMinutes(15);
    $user = User::factory()->create([
        'two_factor_expires_at' => $date,
    ]);
    
    $formatted = $user->two_factor_expires_at;
    $expected = $date->format(config('panel.date_format') . ' ' . config('panel.time_format'));
    
    expect($formatted)->toBe($expected);
});

test('user uses soft deletes', function () {
    $user = User::factory()->create();
    $userId = $user->id;
    
    $user->delete();
    
    expect(User::find($userId))->toBeNull();
    expect(User::withTrashed()->find($userId))->not->toBeNull();
});

