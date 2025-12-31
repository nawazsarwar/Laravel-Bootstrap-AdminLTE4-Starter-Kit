<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\StoreUserRequest;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

test('store user request authorizes user with permission', function () {
    $admin = $this->createAdminUser();
    $permission = Permission::firstOrCreate(['title' => 'user_create']);
    $admin->roles->first()->permissions()->attach($permission);
    $this->actingAs($admin);
    
    // Create a proper request instance
    $httpRequest = \Illuminate\Http\Request::create('/admin/users', 'POST');
    $request = StoreUserRequest::createFrom($httpRequest);
    $request->setContainer(app());
    $request->setRedirector(app('redirect'));
    
    expect($request->authorize())->toBeTrue();
});

test('store user request denies user without permission', function () {
    $user = $this->actingAsUser();
    
    $request = new StoreUserRequest();
    
    expect($request->authorize())->toBeFalse();
});

test('store user request validates required fields', function () {
    $rules = (new StoreUserRequest())->rules();
    
    expect($rules)->toHaveKey('name');
    expect($rules)->toHaveKey('email');
    expect($rules)->toHaveKey('password');
    expect($rules)->toHaveKey('roles');
    
    expect($rules['name'])->toContain('required');
    expect($rules['email'])->toContain('required');
    expect($rules['password'])->toContain('required');
    expect($rules['roles'])->toContain('required');
});

test('store user request validates email is unique', function () {
    $rules = (new StoreUserRequest())->rules();
    
    expect($rules['email'])->toContain('unique:users');
});

test('store user request validates roles is array', function () {
    $rules = (new StoreUserRequest())->rules();
    
    expect($rules['roles'])->toContain('array');
});

test('store user request validates roles items are integers', function () {
    $rules = (new StoreUserRequest())->rules();
    
    expect($rules)->toHaveKey('roles.*');
    expect($rules['roles.*'])->toContain('integer');
});

