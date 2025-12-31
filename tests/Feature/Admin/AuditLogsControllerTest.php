<?php

namespace Tests\Feature\Admin;

use App\Models\AuditLog;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

test('admin can view audit logs index', function () {
    $admin = $this->createAdminUser();
    $permission = Permission::firstOrCreate(['title' => 'audit_log_access']);
    $admin->roles->first()->permissions()->attach($permission);
    $this->actingAs($admin);
    
    $response = $this->get(route('admin.audit-logs.index'));
    
    $response->assertStatus(200);
    $response->assertViewIs('admin.auditLogs.index');
});

test('admin can view audit log', function () {
    $admin = $this->createAdminUser();
    $permission = Permission::firstOrCreate(['title' => 'audit_log_show']);
    $admin->roles->first()->permissions()->attach($permission);
    $this->actingAs($admin);
    
    $auditLog = AuditLog::create([
        'description' => 'test',
        'subject_id' => 1,
        'subject_type' => 'App\Models\User#1',
        'user_id' => $admin->id,
        'properties' => [],
        'host' => '127.0.0.1',
    ]);
    
    $response = $this->get(route('admin.audit-logs.show', $auditLog));
    
    $response->assertStatus(200);
    $response->assertViewIs('admin.auditLogs.show');
});

test('user without permission cannot access audit logs', function () {
    $user = $this->actingAsUser();
    
    $response = $this->get(route('admin.audit-logs.index'));
    
    $response->assertStatus(403);
});

