<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\Helpers\TestHelpers;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, TestHelpers;

    protected function setUp(): void
    {
        parent::setUp();
        // Ensure default roles exist for all tests
        $this->ensureDefaultRoles();
    }
}
