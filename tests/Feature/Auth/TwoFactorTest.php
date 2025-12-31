<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

test('user can view two factor form when code exists', function () {
    $user = User::factory()->create([
        'two_factor_code' => '123456',
        'two_factor_expires_at' => Carbon::now()->addMinutes(15)->format(config('panel.date_format') . ' ' . config('panel.time_format')),
    ]);
    $this->actingAs($user);
    
    $response = $this->get(route('twoFactor.show'));
    
    $response->assertStatus(200);
    $response->assertViewIs('auth.twoFactor');
});

test('user cannot view two factor form when code does not exist', function () {
    $user = User::factory()->create(['two_factor_code' => null]);
    $this->actingAs($user);
    
    $response = $this->get(route('twoFactor.show'));
    
    $response->assertStatus(403);
});

test('user can verify two factor code', function () {
    $user = User::factory()->create([
        'two_factor_code' => '123456',
        'two_factor_expires_at' => Carbon::now()->addMinutes(15)->format(config('panel.date_format') . ' ' . config('panel.time_format')),
    ]);
    $this->actingAs($user);
    
    $response = $this->post(route('twoFactor.check'), [
        'two_factor_code' => '123456',
    ]);
    
    $user->refresh();
    expect($user->two_factor_code)->toBeNull();
    expect($user->two_factor_expires_at)->toBeNull();
});

test('admin user is redirected to admin home after 2fa verification', function () {
    $adminRole = Role::firstOrCreate(['id' => 1], ['title' => 'Admin']);
    $user = User::factory()->create([
        'two_factor_code' => '123456',
        'two_factor_expires_at' => Carbon::now()->addMinutes(15)->format(config('panel.date_format') . ' ' . config('panel.time_format')),
    ]);
    $user->roles()->attach($adminRole);
    $this->actingAs($user);
    
    $response = $this->post(route('twoFactor.check'), [
        'two_factor_code' => '123456',
    ]);
    
    $response->assertRedirect(route('admin.home'));
});

test('regular user is redirected to frontend home after 2fa verification', function () {
    $user = User::factory()->create([
        'two_factor_code' => '123456',
        'two_factor_expires_at' => Carbon::now()->addMinutes(15)->format(config('panel.date_format') . ' ' . config('panel.time_format')),
    ]);
    $this->actingAs($user);
    
    $response = $this->post(route('twoFactor.check'), [
        'two_factor_code' => '123456',
    ]);
    
    $response->assertRedirect(route('frontend.home'));
});

test('user cannot verify with invalid two factor code', function () {
    $user = User::factory()->create([
        'two_factor_code' => '123456',
        'two_factor_expires_at' => Carbon::now()->addMinutes(15)->format(config('panel.date_format') . ' ' . config('panel.time_format')),
    ]);
    $this->actingAs($user);
    
    $response = $this->post(route('twoFactor.check'), [
        'two_factor_code' => '999999',
    ]);
    
    $response->assertSessionHasErrors('two_factor_code');
    $user->refresh();
    expect($user->two_factor_code)->not->toBeNull();
});

test('user can resend two factor code', function () {
    Notification::fake();
    
    $user = User::factory()->create([
        'two_factor_code' => '123456',
        'two_factor_expires_at' => Carbon::now()->addMinutes(15)->format(config('panel.date_format') . ' ' . config('panel.time_format')),
    ]);
    $this->actingAs($user);
    
    $response = $this->get(route('twoFactor.resend'));
    
    Notification::assertSentTo($user, \App\Notifications\TwoFactorCodeNotification::class);
    $response->assertRedirect();
});

test('user cannot resend two factor code when code does not exist', function () {
    $user = User::factory()->create(['two_factor_code' => null]);
    $this->actingAs($user);
    
    $response = $this->get(route('twoFactor.resend'));
    
    $response->assertStatus(403);
});

