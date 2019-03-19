<?php

namespace Tests\Feature;

use Tests\TestCase;

class CraftControllerTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */

    /** @test */
    public function should_execute_craft_controller_command()
    {
        $this->artisan('craft:controller')
             ->expectsOutput('craft:controller handler')
             ->assertExitCode(0);
    }
}
