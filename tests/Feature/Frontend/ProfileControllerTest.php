<?php

namespace Tests\Feature\Frontend;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

test('user can view profile', function () {
    $user = $this->actingAsUser();
    
    $response = $this->get(route('frontend.profile.index'));
    
    $response->assertStatus(200);
    $response->assertViewIs('frontend.profile');
});

test('user can update profile', function () {
    $user = $this->actingAsUser();
    
    $response = $this->post(route('frontend.profile.update'), [
        'name' => 'Updated Name',
        'email' => $user->email,
    ]);
    
    $response->assertRedirect(route('frontend.profile.index'));
    $user->refresh();
    expect($user->name)->toBe('Updated Name');
});

test('user can change password', function () {
    $user = User::factory()->create(['password' => Hash::make('old-password')]);
    $this->actingAs($user);
    
    $response = $this->post(route('frontend.profile.password'), [
        'password' => 'new-password123',
        'password_confirmation' => 'new-password123',
    ]);
    
    $response->assertRedirect(route('frontend.profile.index'));
    $user->refresh();
    expect(Hash::check('new-password123', $user->password))->toBeTrue();
});

test('user can delete account', function () {
    $user = $this->actingAsUser();
    $userId = $user->id;
    
    $response = $this->post(route('frontend.profile.destroy'));
    
    $response->assertRedirect(route('login'));
    $this->assertSoftDeleted('users', ['id' => $userId]);
});

test('user can toggle two factor authentication', function () {
    $user = $this->actingAsUser();
    expect($user->two_factor)->toBeFalse();
    
    $response = $this->post(route('frontend.profile.toggle-two-factor'));
    
    $response->assertRedirect(route('frontend.profile.index'));
    $user->refresh();
    expect($user->two_factor)->toBeTrue();
    
    $response = $this->post(route('frontend.profile.toggle-two-factor'));
    $user->refresh();
    expect($user->two_factor)->toBeFalse();
});

