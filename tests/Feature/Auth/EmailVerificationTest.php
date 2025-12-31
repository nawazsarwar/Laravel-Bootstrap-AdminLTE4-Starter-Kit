<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

test('unverified user can view verification notice', function () {
    $user = User::factory()->create(['email_verified_at' => null]);
    $this->actingAs($user);
    
    $response = $this->get(route('verification.notice'));
    
    $response->assertStatus(200);
});

test('user can verify email with valid token', function () {
    $user = User::factory()->create(['email_verified_at' => null]);
    $this->actingAs($user);
    
    $verificationUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)]
    );
    
    $response = $this->get($verificationUrl);
    
    $response->assertRedirect('/home');
    $user->refresh();
    expect($user->email_verified_at)->not->toBeNull();
});

test('user cannot verify email with invalid token', function () {
    $user = User::factory()->create(['email_verified_at' => null]);
    $this->actingAs($user);
    
    $response = $this->get(route('verification.verify', ['id' => $user->id, 'hash' => 'invalid-hash']));
    
    $response->assertStatus(403);
});

test('user can resend verification email', function () {
    Notification::fake();
    
    $user = User::factory()->create(['email_verified_at' => null]);
    $this->actingAs($user);
    
    $response = $this->post(route('verification.resend'));
    
    $response->assertRedirect();
    Notification::assertSentTo($user, \Illuminate\Auth\Notifications\VerifyEmail::class);
});

