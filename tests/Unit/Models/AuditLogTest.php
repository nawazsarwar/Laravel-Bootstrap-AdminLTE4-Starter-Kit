<?php

namespace Tests\Unit\Models;

use App\Models\AuditLog;
use App\Models\User;
use Tests\TestCase;

test('audit log casts properties to collection', function () {
    $auditLog = AuditLog::create([
        'description' => 'test',
        'subject_id' => 1,
        'subject_type' => 'App\Models\User#1',
        'user_id' => 1,
        'properties' => ['key' => 'value'],
        'host' => '127.0.0.1',
    ]);
    
    expect($auditLog->properties)->toBeInstanceOf(\Illuminate\Support\Collection::class);
    expect($auditLog->properties->get('key'))->toBe('value');
});

test('audit log serializes date correctly', function () {
    $auditLog = AuditLog::create([
        'description' => 'test',
        'subject_id' => 1,
        'subject_type' => 'App\Models\User#1',
        'user_id' => 1,
        'properties' => [],
        'host' => '127.0.0.1',
    ]);
    
    $serialized = $auditLog->toArray();
    
    expect($serialized['created_at'])->toMatch('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/');
});

