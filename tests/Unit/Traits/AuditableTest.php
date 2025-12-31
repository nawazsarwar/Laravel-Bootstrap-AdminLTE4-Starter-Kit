<?php

namespace Tests\Unit\Traits;

use App\Models\AuditLog;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

test('auditable trait creates audit log on model creation', function () {
    $user = $this->actingAsAdmin();
    
    $permission = Permission::factory()->create();
    
    $auditLog = AuditLog::where('subject_type', 'like', '%Permission%')
        ->where('subject_id', $permission->id)
        ->where('description', 'audit:created')
        ->first();
    
    expect($auditLog)->not->toBeNull();
    expect($auditLog->user_id)->toBe($user->id);
    expect($auditLog->host)->not->toBeNull();
});

test('auditable trait creates audit log on model update with changes', function () {
    $user = $this->actingAsAdmin();
    
    $permission = Permission::factory()->create(['title' => 'Original Title']);
    $permission->update(['title' => 'Updated Title']);
    
    $auditLog = AuditLog::where('subject_type', 'like', '%Permission%')
        ->where('subject_id', $permission->id)
        ->where('description', 'audit:updated')
        ->first();
    
    expect($auditLog)->not->toBeNull();
    expect($auditLog->properties)->toHaveKey('title');
    expect($auditLog->properties->get('title'))->toBe('Updated Title');
    expect($auditLog->user_id)->toBe($user->id);
});

test('auditable trait creates audit log on model deletion', function () {
    $user = $this->actingAsAdmin();
    
    $permission = Permission::factory()->create();
    $permissionId = $permission->id;
    $permission->delete();
    
    $auditLog = AuditLog::where('subject_type', 'like', '%Permission%')
        ->where('subject_id', $permissionId)
        ->where('description', 'audit:deleted')
        ->first();
    
    expect($auditLog)->not->toBeNull();
    expect($auditLog->user_id)->toBe($user->id);
});

test('auditable trait captures ip address', function () {
    $user = $this->actingAsAdmin();
    
    $permission = Permission::factory()->create();
    
    $auditLog = AuditLog::where('subject_type', 'like', '%Permission%')
        ->where('subject_id', $permission->id)
        ->first();
    
    expect($auditLog->host)->not->toBeNull();
});

