<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\UpdateRoleRequest;
use App\Models\Permission;
use Tests\TestCase;

test('update role request authorizes user with permission', function () {
    $admin = $this->createAdminUser();
    $permission = Permission::firstOrCreate(['title' => 'role_edit']);
    $admin->roles->first()->permissions()->attach($permission);
    $this->actingAs($admin);
    
    $httpRequest = \Illuminate\Http\Request::create('/admin/roles/1', 'PUT');
    $request = UpdateRoleRequest::createFrom($httpRequest);
    $request->setContainer(app());
    $request->setRedirector(app('redirect'));
    
    expect($request->authorize())->toBeTrue();
});

test('update role request validates required fields', function () {
    $rules = (new UpdateRoleRequest())->rules();
    
    expect($rules)->toHaveKey('title');
    expect($rules)->toHaveKey('permissions');
    
    expect($rules['title'])->toContain('required');
    expect($rules['permissions'])->toContain('required');
});

