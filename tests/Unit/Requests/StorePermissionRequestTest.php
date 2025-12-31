<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\StorePermissionRequest;
use App\Models\Permission;
use Tests\TestCase;

test('store permission request authorizes user with permission', function () {
    $admin = $this->createAdminUser();
    $permission = Permission::firstOrCreate(['title' => 'permission_create']);
    $admin->roles->first()->permissions()->attach($permission);
    $this->actingAs($admin);
    
    $httpRequest = \Illuminate\Http\Request::create('/admin/permissions', 'POST');
    $request = StorePermissionRequest::createFrom($httpRequest);
    $request->setContainer(app());
    $request->setRedirector(app('redirect'));
    
    expect($request->authorize())->toBeTrue();
});

test('store permission request validates required fields', function () {
    $rules = (new StorePermissionRequest())->rules();
    
    expect($rules)->toHaveKey('title');
    expect($rules['title'])->toContain('required');
    expect($rules['title'])->toContain('string');
});

