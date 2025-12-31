<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\UpdateProfileRequest;
use Tests\TestCase;

test('update profile request validates name field', function () {
    $rules = (new UpdateProfileRequest())->rules();
    
    expect($rules)->toHaveKey('name');
    expect($rules['name'])->toContain('required');
});

test('update profile request validates email field', function () {
    $rules = (new UpdateProfileRequest())->rules();
    
    expect($rules)->toHaveKey('email');
    expect($rules['email'])->toContain('required');
    expect($rules['email'])->toContain('email');
});

