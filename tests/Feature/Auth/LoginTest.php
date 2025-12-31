<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

test('user can view login form', function () {
    $response = $this->get(route('login'));
    
    $response->assertStatus(200);
    $response->assertViewIs('auth.login');
});

test('user can login with valid credentials', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password123'),
    ]);
    
    $response = $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password123',
    ]);
    
    $response->assertRedirect();
    $this->assertAuthenticatedAs($user);
});

test('user cannot login with invalid credentials', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password123'),
    ]);
    
    $response = $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);
    
    $response->assertSessionHasErrors();
    $this->assertGuest();
});

test('admin user is redirected to admin panel after login', function () {
    $adminRole = Role::firstOrCreate(['id' => 1], ['title' => 'Admin']);
    $user = User::factory()->create([
        'password' => bcrypt('password123'),
    ]);
    $user->roles()->attach($adminRole);
    
    $response = $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password123',
    ]);
    
    $response->assertRedirect('/admin');
});

test('regular user is redirected to home after login', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password123'),
    ]);
    
    $response = $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password123',
    ]);
    
    $response->assertRedirect('/home');
});

test('two factor code is generated when user has 2fa enabled', function () {
    Notification::fake();
    
    $user = User::factory()->create([
        'password' => bcrypt('password123'),
        'two_factor' => true,
    ]);
    
    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password123',
    ]);
    
    $user->refresh();
    expect($user->two_factor_code)->not->toBeNull();
});

test('two factor notification is sent when user has 2fa enabled', function () {
    Notification::fake();
    
    $user = User::factory()->create([
        'password' => bcrypt('password123'),
        'two_factor' => true,
    ]);
    
    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password123',
    ]);
    
    Notification::assertSentTo($user, \App\Notifications\TwoFactorCodeNotification::class);
});

test('user can logout', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    
    $response = $this->post(route('logout'));
    
    $response->assertRedirect('/');
    $this->assertGuest();
});

