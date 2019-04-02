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
        $this->withoutExceptionHandling();
    }

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

        $data = file_get_contents($filename);
        $this->assertStringContainsString("use {$model_path};", $data);

        unlink($filename);
    }
}
