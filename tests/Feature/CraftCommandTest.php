<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\TestHelpersTrait;
use App\CraftsmanFileSystem;

/**
 * Class CraftCommandTest
 * @package Tests\Feature
 */
class CraftCommandTest extends TestCase
{
    use TestHelpersTrait;

    /**
     * @var CraftsmanFileSystem
     */
    protected $fs;

    /**
     *
     */
    function setUp(): void
    {
        parent::setUp();

        $this->fs = new CraftsmanFileSystem();

        $this->withoutExceptionHandling();
    }

    /** @test */
    public function should_create_console_command()
    {
        $class = "TestCommand";

        $this->artisan("craft:command {$class}")
            ->assertExitCode(0);

        $filename = $this->pathJoin("app", "Console", "Commands", "{$class}.php");
        $this->assertFileExists($filename);

        $this->assertFileContainsString($filename, "class {$class}");

        unlink($filename);
    }

    public function should_create_class_with_namespace()
    {
        $class = "App/Test/SampleClass";

        $this->artisan("craft:class {$class}")
            ->assertExitCode(0);

        $filename = $this->pathJoin("app/Test", "SampleClass.php");
        $this->assertFileExists($filename);

        $this->assertFileContainsString($filename, "class SampleClass");

        $this->fs->rmdir("app/Test");
    }

    public function should_create_class_using_user_template()
    {
        $class = "App/Test/SampleClass";

        $this->artisan("craft:class {$class} --template <project>/templates/custom.mustache")
            ->assertExitCode(0);

        $filename = $this->pathJoin("app/Test", "SampleClass.php");
        $this->assertFileExists($filename);

        $this->assertFileContainsString($filename, "class SampleClass");
        $this->assertFileContainsString($filename, "testMethod");

        $this->fs->rmdir("app/Test");
    }
}
