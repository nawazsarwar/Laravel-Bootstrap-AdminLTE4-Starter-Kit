<?php

namespace Tests\Feature\Frontend;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

test('user can view frontend home', function () {
    $user = $this->actingAsUser();
    
    $response = $this->get(route('frontend.home'));
    
    $response->assertStatus(200);
    $response->assertViewIs('frontend.home');
});

