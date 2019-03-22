<?php

namespace Tests\Feature;

use Tests\TestCase;

class CraftModelTest extends TestCase
{
    /** @test */
    public function should_execute_default_craft_model_command()
    {
        $this->artisan('craft:model Post')
            ->assertExitCode(0);
    }

    /** @test */
    public function should_execute_custom_craft_model_command()
    {
        $this->artisan('craft:model App/Models/Post')
            ->assertExitCode(0);
    }
}
