<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\StoreRoleRequest;
use App\Models\Permission;
use Tests\TestCase;

test('store role request authorizes user with permission', function () {
    $admin = $this->createAdminUser();
    $permission = Permission::firstOrCreate(['title' => 'role_create']);
    $admin->roles->first()->permissions()->attach($permission);
    $this->actingAs($admin);
    
    $httpRequest = \Illuminate\Http\Request::create('/admin/roles', 'POST');
    $request = StoreRoleRequest::createFrom($httpRequest);
    $request->setContainer(app());
    $request->setRedirector(app('redirect'));
    
    expect($request->authorize())->toBeTrue();
});

test('store role request validates required fields', function () {
    $rules = (new StoreRoleRequest())->rules();
    
    expect($rules)->toHaveKey('title');
    expect($rules)->toHaveKey('permissions');
    
    expect($rules['title'])->toContain('required');
    expect($rules['permissions'])->toContain('required');
    expect($rules['permissions'])->toContain('array');
});

test('store role request validates permissions items are integers', function () {
    $rules = (new StoreRoleRequest())->rules();
    
    expect($rules)->toHaveKey('permissions.*');
    expect($rules['permissions.*'])->toContain('integer');
});

