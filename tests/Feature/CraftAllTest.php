<?php

namespace Tests\Feature;

use Tests\TestCase;

class CraftAllTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */

    /** @test */
    public function should_execute_craft_all_command()
    {
        $this->artisan('craft:all')
             ->expectsOutput('craft:all handler')
             ->assertExitCode(0);
    }
}
