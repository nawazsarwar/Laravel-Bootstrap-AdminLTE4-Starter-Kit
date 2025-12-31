<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

test('admin can view admin home', function () {
    $admin = $this->actingAsAdmin();
    
    $response = $this->get(route('admin.home'));
    
    $response->assertStatus(200);
    $response->assertViewIs('home');
});

