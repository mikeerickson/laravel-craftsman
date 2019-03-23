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
        $this->withoutExceptionHandling();
    }

    /** @test  */
    public function should_create_empty_controller()
    {
        $this->artisan("craft:controller EmptyController")
            ->assertExitCode(0);

        $controllerPath = $this->fs->controller_path();
        $filename = $this->fs->path_join($controllerPath, "EmptyController.php");
        $this->assertFileExists($filename);
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
        $this->assertStringContainsString("public function index()", $data);
        $this->assertStringContainsString("use {$model_path};", $data);

        $this->assertStringContainsString("edit", $data);
        $this->assertStringContainsString("create", $data);
    }

    /** @test  */
    public function should_create_api_controller()
    {
        $model = "App/Models/Contact";
        $model_path = "App\\Models\\Contact";

        $this->artisan("craft:controller ContactAPIController --model {$model} --api")
            ->assertExitCode(0);

        $controllerPath = $this->fs->controller_path();
        $filename = $this->fs->path_join($controllerPath, "ContactAPIController.php");
        $this->assertFileExists($filename);

        $parts = explode("/", $model);
        $model_name = array_pop($parts);

        // spot check merged data
        $data = file_get_contents($filename);

        $this->assertStringContainsString("class {$model_name}", $data);
        $this->assertStringContainsString("return {$model_name}::all();", $data);
        $this->assertStringContainsString("use {$model_path};", $data);

        $this->assertStringNotContainsString("edit", $data);
        $this->assertStringNotContainsString("create", $data);
    }
}
