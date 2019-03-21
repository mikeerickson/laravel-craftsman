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
        $this->artisan('craft:seed TestsTableSeeder --model App/Models/Test --rows 25')
            ->assertExitCode(0);
    }
}
