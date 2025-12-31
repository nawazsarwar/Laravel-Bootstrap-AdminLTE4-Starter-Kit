<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\UpdatePasswordRequest;
use Tests\TestCase;

test('update password request validates password fields', function () {
    $rules = (new UpdatePasswordRequest())->rules();
    
    expect($rules)->toHaveKey('password');
    expect($rules['password'])->toContain('required');
});

test('update password request validates password confirmation', function () {
    $rules = (new UpdatePasswordRequest())->rules();
    
    expect($rules['password'])->toContain('confirmed');
});

