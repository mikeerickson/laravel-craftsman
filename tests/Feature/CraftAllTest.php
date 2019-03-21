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
        $this->artisan('craft:all Author --model App/Models/Author --table authors --rows 44')
            ->assertExitCode(0);
        $this->markTestIncomplete("WIP");
    }
}
