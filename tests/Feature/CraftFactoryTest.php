<?php

namespace Tests\Feature;

use Tests\TestCase;

class CraftFactoryTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */

    /** @test */
    public function should_execute_craft_factory_command()
    {
        $this->artisan('craft:factory')
             ->expectsOutput('craft:factory handler')
             ->assertExitCode(0);
    }
}
