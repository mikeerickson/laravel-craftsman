<?php

namespace Tests\Feature;

use App\CraftsmanFileSystem;
use Tests\TestCase;

class CraftControllerTest extends TestCase
{
    protected $fs;

    function setUp(): void
    {
        parent::setUp();
        $this->fs = new CraftsmanFileSystem();
    }

    /** @test */
    public function should_execute_craft_controller_command()
    {
        $model = "App/Models/Test";
        $model_path = "App\\Models\\Test";

        $this->artisan("craft:controller TestController --model {$model}")
            ->assertExitCode(0);

        $controllerPath = $this->fs->controller_path();
        $filename = $this->fs->path_join($controllerPath, "TestController.php");
        $this->assertFileExists($filename);

        $parts = explode("/", $model);
        $model_name = array_pop($parts);

        // spot check merged data
        $data = file_get_contents($filename);
        $this->assertStringContainsString("class {$model_name}", $data);
        $this->assertStringContainsString("return {$model_name}::all();", $data);
        $this->assertStringContainsString("use {$model_path};", $data);
    }
}
