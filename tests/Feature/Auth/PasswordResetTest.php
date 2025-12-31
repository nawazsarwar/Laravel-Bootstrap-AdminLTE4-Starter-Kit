<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

test('user can view password reset request form', function () {
    $response = $this->get(route('password.request'));
    
    $response->assertStatus(200);
});

test('user can request password reset', function () {
    Notification::fake();
    
    $user = User::factory()->create();
    
    $response = $this->post(route('password.email'), [
        'email' => $user->email,
    ]);
    
    $response->assertStatus(302);
    Notification::assertSentTo($user, \Illuminate\Auth\Notifications\ResetPassword::class);
});

test('user cannot request password reset with invalid email', function () {
    $response = $this->post(route('password.email'), [
        'email' => 'nonexistent@example.com',
    ]);
    
    $response->assertSessionHasErrors('email');
});

test('user can view password reset form with valid token', function () {
    $user = User::factory()->create();
    $token = \Illuminate\Support\Facades\Password::createToken($user);
    
    $response = $this->get(route('password.reset', ['token' => $token]));
    
    $response->assertStatus(200);
});

test('user can reset password with valid token', function () {
    $user = User::factory()->create(['password' => Hash::make('old-password')]);
    $token = \Illuminate\Support\Facades\Password::createToken($user);
    
    $response = $this->post(route('password.update'), [
        'token' => $token,
        'email' => $user->email,
        'password' => 'new-password123',
        'password_confirmation' => 'new-password123',
    ]);
    
    $response->assertRedirect(route('login'));
    $user->refresh();
    expect(Hash::check('new-password123', $user->password))->toBeTrue();
});

test('user cannot reset password with invalid token', function () {
    $user = User::factory()->create();
    
    $response = $this->post(route('password.update'), [
        'token' => 'invalid-token',
        'email' => $user->email,
        'password' => 'new-password123',
        'password_confirmation' => 'new-password123',
    ]);
    
    $response->assertSessionHasErrors('email');
});

test('user cannot reset password without password confirmation', function () {
    $user = User::factory()->create();
    $token = \Illuminate\Support\Facades\Password::createToken($user);
    
    $response = $this->post(route('password.update'), [
        'token' => $token,
        'email' => $user->email,
        'password' => 'new-password123',
        'password_confirmation' => 'different-password',
    ]);
    
    $response->assertSessionHasErrors('password');
});

