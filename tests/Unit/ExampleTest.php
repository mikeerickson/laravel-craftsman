<?php

namespace App\Unit;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();
}

/** @test */
public function should_pass()
{
$this->assertTrue(true);
}

    function tearDown(): void
    {
    parent::tearDown();
    }
}
