<?php

namespace Tests\Feature;

use Tests\TestCase;

class CraftSeedTest extends TestCase
{
    function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();
    }

    /** @test */
    public function should_execute_craft_seed_command()
    {
        $this->artisan('craft:seed TestsTableSeeder --model App/Models/Test --rows 25')
            ->assertExitCode(0);
    }
}
