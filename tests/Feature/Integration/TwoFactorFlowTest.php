<?php

namespace Tests\Feature\Integration;

use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

test('complete two factor authentication flow works', function () {
    Notification::fake();
    
    // Step 1: User enables 2FA
    $user = User::factory()->create([
        'password' => bcrypt('password123'),
        'two_factor' => false,
    ]);
    $this->actingAs($user);
    
    $response = $this->post(route('frontend.profile.toggle-two-factor'));
    $response->assertRedirect(route('frontend.profile.index'));
    
    $user->refresh();
    expect($user->two_factor)->toBeTrue();
    
    // Step 2: User logs out
    $this->post(route('logout'));
    $this->assertGuest();
    
    // Step 3: User logs in - 2FA code should be generated
    $response = $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password123',
    ]);
    
    $user->refresh();
    expect($user->two_factor_code)->not->toBeNull();
    expect($user->two_factor_expires_at)->not->toBeNull();
    Notification::assertSentTo($user, \App\Notifications\TwoFactorCodeNotification::class);
    
    // Step 4: User is redirected to 2FA page
    $response->assertRedirect(route('twoFactor.show'));
    
    // Step 5: User enters correct 2FA code
    $response = $this->post(route('twoFactor.check'), [
        'two_factor_code' => $user->two_factor_code,
    ]);
    
    $user->refresh();
    expect($user->two_factor_code)->toBeNull();
    expect($user->two_factor_expires_at)->toBeNull();
    
    // Step 6: User is redirected to home
    $response->assertRedirect(route('frontend.home'));
    $this->assertAuthenticatedAs($user);
});

test('two factor authentication blocks access with expired code', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password123'),
        'two_factor' => true,
        'two_factor_code' => '123456',
        'two_factor_expires_at' => Carbon::now()->subMinutes(20)->format(config('panel.date_format') . ' ' . config('panel.time_format')),
    ]);
    
    $this->actingAs($user);
    
    // Try to access protected route
    $response = $this->get(route('frontend.home'));
    
    // Should be redirected to login due to expired code
    $response->assertRedirect(route('login'));
    expect(auth()->check())->toBeFalse();
});

test('two factor authentication allows resending code', function () {
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

