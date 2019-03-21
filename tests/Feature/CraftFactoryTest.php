<?php

namespace Tests\Feature;

use App\CraftsmanFileSystem;
use Tests\TestCase;

class CraftFactoryTest extends TestCase
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
    public function should_execute_craft_factory_command()
    {
        $model = "App/Models/Test";
        $model_path = "App\\Models\\Test";

        $this->artisan("craft:factory TestFactory --model {$model}")
            ->assertExitCode(0);

        $factoryPath = $this->fs->factory_path();
        $filename = $this->fs->path_join($factoryPath, "TestFactory.php");
        $this->assertFileExists($filename);

        $parts = explode("/", $model);
        $model_name = array_pop($parts);

        // spot check merged data
        $data = file_get_contents($filename);
        $this->assertStringContainsString("use {$model_path};", $data);

    }
}
