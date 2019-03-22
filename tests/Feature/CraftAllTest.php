<?php

namespace Tests\Feature;

use Tests\TestCase;

class CraftAllTest extends TestCase
{
    /** @test */
    public function should_execute_craft_all_command()
    {
        $this->artisan('craft:all Author --model App/Models/Author --table authors --rows 44')
            ->assertExitCode(0);
    }
}
