<?php

namespace Tests\Feature;

use Tests\TestCase;

class CraftMigrationTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */

    /** @test */
    public function should_execute_craft_migration_command()
    {
        $this->artisan('craft:migration')
             ->expectsOutput('craft:migration handler')
             ->assertExitCode(0);
    }
}
