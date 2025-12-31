<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\TestCase;

test('user can view registration form', function () {
    $response = $this->get(route('register'));
    
    $response->assertStatus(200);
    $response->assertViewIs('auth.register');
});

test('user can register with valid data', function () {
    Notification::fake();
    
    $response = $this->post(route('register'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);
    
    $response->assertRedirect();
    $this->assertDatabaseHas('users', [
        'email' => 'test@example.com',
        'name' => 'Test User',
    ]);
});

test('user registration creates verification token', function () {
    Notification::fake();
    auth()->logout();
    
    $this->post(route('register'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);
    
    $user = User::where('email', 'test@example.com')->first();
    expect($user->verification_token)->not->toBeNull();
    expect(Str::length($user->verification_token))->toBe(64);
});

test('user registration assigns default role', function () {
    Notification::fake();
    auth()->logout();
    
    $defaultRoleId = config('panel.registration_default_role', '2');
    $defaultRole = Role::firstOrCreate(['id' => $defaultRoleId], ['title' => 'User']);
    
    $this->post(route('register'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);
    
    $user = User::where('email', 'test@example.com')->first();
    expect($user->roles)->toHaveCount(1);
    expect($user->roles->first()->id)->toBe((int)$defaultRoleId);
});

test('user registration sends verification notification', function () {
    Notification::fake();
    auth()->logout();
    
    $this->post(route('register'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);
    
    $user = User::where('email', 'test@example.com')->first();
    Notification::assertSentTo($user, \App\Notifications\VerifyUserNotification::class);
});

test('user registration requires name', function () {
    $response = $this->post(route('register'), [
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);
    
    $response->assertSessionHasErrors('name');
});

test('user registration requires valid email', function () {
    $response = $this->post(route('register'), [
        'name' => 'Test User',
        'email' => 'invalid-email',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);
    
    $response->assertSessionHasErrors('email');
});

test('user registration requires unique email', function () {
    User::factory()->create(['email' => 'test@example.com']);
    
    $response = $this->post(route('register'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);
    
    $response->assertSessionHasErrors('email');
});

test('user registration requires password confirmation', function () {
    $response = $this->post(route('register'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'different-password',
    ]);
    
    $response->assertSessionHasErrors('password');
});

test('user registration requires minimum password length', function () {
    $response = $this->post(route('register'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'short',
        'password_confirmation' => 'short',
    ]);
    
    $response->assertSessionHasErrors('password');
});

test('user created by admin is auto verified', function () {
    $admin = $this->actingAsAdmin();
    
    $response = $this->post(route('admin.users.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'roles' => [1],
    ]);
    
    $user = User::where('email', 'test@example.com')->first();
    expect($user->verified)->toBe(1);
    expect($user->verified_at)->not->toBeNull();
});

