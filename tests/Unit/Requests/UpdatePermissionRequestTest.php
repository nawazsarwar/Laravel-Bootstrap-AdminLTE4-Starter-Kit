<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\UpdatePermissionRequest;
use App\Models\Permission;
use Tests\TestCase;

test('update permission request authorizes user with permission', function () {
    $admin = $this->createAdminUser();
    $permission = Permission::firstOrCreate(['title' => 'permission_edit']);
    $admin->roles->first()->permissions()->attach($permission);
    $this->actingAs($admin);
    
    $httpRequest = \Illuminate\Http\Request::create('/admin/permissions/1', 'PUT');
    $request = UpdatePermissionRequest::createFrom($httpRequest);
    $request->setContainer(app());
    $request->setRedirector(app('redirect'));
    
    expect($request->authorize())->toBeTrue();
});

test('update permission request validates required fields', function () {
    $rules = (new UpdatePermissionRequest())->rules();
    
    expect($rules)->toHaveKey('title');
    expect($rules['title'])->toContain('required');
});

