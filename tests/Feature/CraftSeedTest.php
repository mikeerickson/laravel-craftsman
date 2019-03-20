<?php

namespace Tests\Feature;

use Tests\TestCase;

class CraftSeedTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */

    /** @test */
    public function should_execute_craft_seed_command()
    {
        $this->artisan('craft:seed')
            ->expectsOutput('craft:seed handler')
            ->assertExitCode(0);
    }
}
