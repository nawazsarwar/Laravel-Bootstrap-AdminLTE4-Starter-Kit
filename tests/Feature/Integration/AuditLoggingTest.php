<?php

namespace Tests\Feature\Integration;

use App\Models\AuditLog;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

test('audit logging works for create update delete operations', function () {
    $admin = $this->actingAsAdmin();
    
    // Step 1: Create permission - should create audit log
    $permission = Permission::factory()->create(['title' => 'test_permission']);
    
    $auditLog = AuditLog::where('subject_type', 'like', '%Permission%')
        ->where('subject_id', $permission->id)
        ->where('description', 'audit:created')
        ->first();
    
    expect($auditLog)->not->toBeNull();
    expect($auditLog->user_id)->toBe($admin->id);
    expect($auditLog->host)->not->toBeNull();
    
    // Step 2: Update permission - should create audit log with changes
    $permission->update(['title' => 'updated_permission']);
    
    $auditLog = AuditLog::where('subject_type', 'like', '%Permission%')
        ->where('subject_id', $permission->id)
        ->where('description', 'audit:updated')
        ->first();
    
    expect($auditLog)->not->toBeNull();
    expect($auditLog->properties)->toHaveKey('title');
    expect($auditLog->properties->get('title'))->toBe('updated_permission');
    
    // Step 3: Delete permission - should create audit log
    $permissionId = $permission->id;
    $permission->delete();
    
    $auditLog = AuditLog::where('subject_type', 'like', '%Permission%')
        ->where('subject_id', $permissionId)
        ->where('description', 'audit:deleted')
        ->first();
    
    expect($auditLog)->not->toBeNull();
    
    // Step 4: Verify all audit logs are created
    $allLogs = AuditLog::where('subject_id', $permissionId)->get();
    expect($allLogs)->toHaveCount(3); // created, updated, deleted
});

test('audit logging captures user and ip for role operations', function () {
    $admin = $this->actingAsAdmin();
    
    // Create role
    $role = Role::factory()->create(['title' => 'Test Role']);
    
    $auditLog = AuditLog::where('subject_type', 'like', '%Role%')
        ->where('subject_id', $role->id)
        ->where('description', 'audit:created')
        ->first();
    
    expect($auditLog->user_id)->toBe($admin->id);
    expect($auditLog->host)->not->toBeNull();
});

