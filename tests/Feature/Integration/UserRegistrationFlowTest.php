<?php

namespace Tests\Feature\Integration;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\TestCase;

test('complete user registration flow works', function () {
    Notification::fake();
    auth()->logout();
    
    // Step 1: User registers
    $response = $this->post(route('register'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);
    
    $response->assertRedirect();
    
    // Step 2: Verify user was created with verification token
    $user = User::where('email', 'test@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->verified)->toBe(0);
    expect($user->verification_token)->not->toBeNull();
    
    // Step 3: Verify default role was assigned
    $defaultRoleId = config('panel.registration_default_role', '2');
    expect($user->roles)->toHaveCount(1);
    expect($user->roles->first()->id)->toBe((int)$defaultRoleId);
    
    // Step 4: User clicks verification link
    $response = $this->get(route('userVerification', ['token' => $user->verification_token]));
    $response->assertRedirect(route('login'));
    
    // Step 5: Verify user is now verified
    $user->refresh();
    expect($user->verified)->toBe(1);
    expect($user->verified_at)->not->toBeNull();
    expect($user->verification_token)->toBeNull();
    
    // Step 6: User can now login
    $response = $this->post(route('login'), [
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);
    
    $response->assertRedirect();
    $this->assertAuthenticatedAs($user);
});

