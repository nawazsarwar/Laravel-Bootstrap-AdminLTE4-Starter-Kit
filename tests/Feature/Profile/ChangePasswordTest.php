<?php

namespace Tests\Feature\Profile;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

test('user can view change password form', function () {
    $user = $this->actingAsUser();
    $permission = Permission::firstOrCreate(['title' => 'profile_password_edit']);
    $user->roles->first()->permissions()->attach($permission);
    
    $response = $this->get(route('profile.password.edit'));
    
    $response->assertStatus(200);
    $response->assertViewIs('auth.passwords.edit');
});

test('user can update password', function () {
    $user = User::factory()->create(['password' => Hash::make('old-password')]);
    $permission = Permission::firstOrCreate(['title' => 'profile_password_edit']);
    $user->roles->first()->permissions()->attach($permission);
    $this->actingAs($user);
    
    $response = $this->post(route('profile.password.update'), [
        'password' => 'new-password123',
        'password_confirmation' => 'new-password123',
    ]);
    
    $response->assertRedirect(route('profile.password.edit'));
    $user->refresh();
    expect(Hash::check('new-password123', $user->password))->toBeTrue();
});

test('user can update profile', function () {
    $user = $this->actingAsUser();
    $permission = Permission::firstOrCreate(['title' => 'profile_password_edit']);
    $user->roles->first()->permissions()->attach($permission);
    
    $response = $this->post(route('profile.password.updateProfile'), [
        'name' => 'Updated Name',
        'email' => $user->email,
    ]);
    
    $response->assertRedirect(route('profile.password.edit'));
    $user->refresh();
    expect($user->name)->toBe('Updated Name');
});

test('user can delete account', function () {
    $user = $this->actingAsUser();
    $userId = $user->id;
    
    $response = $this->post(route('profile.password.destroyProfile'));
    
    $response->assertRedirect(route('login'));
    $this->assertSoftDeleted('users', ['id' => $userId]);
});

test('user can toggle two factor authentication', function () {
    $user = $this->actingAsUser();
    expect($user->two_factor)->toBeFalse();
    
    $response = $this->post(route('profile.password.toggleTwoFactor'));
    
    $response->assertRedirect(route('profile.password.edit'));
    $user->refresh();
    expect($user->two_factor)->toBeTrue();
});

test('user without permission cannot access change password form', function () {
    $user = $this->actingAsUser();
    
    $response = $this->get(route('profile.password.edit'));
    
    $response->assertStatus(403);
});

