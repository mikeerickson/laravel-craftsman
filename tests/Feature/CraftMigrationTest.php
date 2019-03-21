<?php

namespace Tests\Feature;

use App\CraftsmanFileSystem;
use Tests\TestCase;

class CraftMigrationTest extends TestCase
{

    protected $fs;

    function setUp(): void
    {
        parent::setUp();
        $this->fs = new CraftsmanFileSystem();
    }
    /**
     * A basic test example.
     *
     * @return void
     */

    /** @test */
    public function should_execute_default_craft_migration_command()
    {
        $model = "App/Models/Test";
        $model_path = "App\\Models\\Test";

        // 2019_03_18_193911_create_widgets_table
        $this->artisan("craft:migration create_tests_table --model {$model}")
            ->assertExitCode(0);
    }

    /** @test */
    public function should_execute_craft_migration_command_with_table()
    {
        $this->assertTrue(true);

    }

}
