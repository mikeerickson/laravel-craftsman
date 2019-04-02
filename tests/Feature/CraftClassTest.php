<?php

namespace Tests\Feature;

use App\CraftsmanFileSystem;
use Tests\TestCase;

class CraftClassTest extends TestCase
{
    protected $fs;

    function setUp(): void
    {
        parent::setUp();
        $this->fs = new CraftsmanFileSystem();
        $this->withoutExceptionHandling();
    }

    /** @test */
    public function should_create_simple_class_command()
    {
        $class = "Test";

        $this->artisan("craft:class {$class}")
            ->assertExitCode(0);

        $filename = $this->fs->path_join("app", "Test.php");
        $this->assertFileExists($filename);

        $data = file_get_contents($filename);

        unlink($filename);
//        $this->assertStringContainsString("use {$model_path};", $data);
    }

    /** @test */
    public function should_create_class_with_namespace()
    {
        $class = "App/Test/SampleClass";

        $this->artisan("craft:class {$class}")
            ->assertExitCode(0);

        $filename = $this->fs->path_join("app/Test", "SampleClass.php");
        $this->assertFileExists($filename);

        $data = file_get_contents($filename);
        $this->assertStringContainsString("class SampleClass", $data);
    }
}
