<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

test('user can be verified with valid token', function () {
    $token = Str::random(64);
    $user = User::factory()->create([
        'verified' => false,
        'verification_token' => $token,
    ]);
    
    $response = $this->get(route('userVerification', ['token' => $token]));
    
    $response->assertRedirect(route('login'));
    $user->refresh();
    expect($user->verified)->toBe(1);
    expect($user->verified_at)->not->toBeNull();
    expect($user->verification_token)->toBeNull();
});

test('user verification returns 404 for invalid token', function () {
    $response = $this->get(route('userVerification', ['token' => 'invalid-token']));
    
    $response->assertStatus(404);
});

test('user verification clears verification token', function () {
    $token = Str::random(64);
    $user = User::factory()->create([
        'verified' => false,
        'verification_token' => $token,
    ]);
    
    $this->get(route('userVerification', ['token' => $token]));
    
    $user->refresh();
    expect($user->verification_token)->toBeNull();
});

