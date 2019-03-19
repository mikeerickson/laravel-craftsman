<?php

namespace Tests\Feature;

use Tests\TestCase;

class CraftModelTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */

    /** @test */
    public function should_execute_craft_model_command()
    {
        $this->artisan('craft:model')
             ->expectsOutput('craft:model handler')
             ->assertExitCode(0);
    }
}
