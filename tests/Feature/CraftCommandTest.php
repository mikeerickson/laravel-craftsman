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

    /** @test */
    public function should_create_command_with_namespace()
    {
        $class = "App/Test/SampleClass";

        $this->artisan("craft:class {$class}")
            ->assertExitCode(0);

        $filename = $this->pathJoin("app/Test", "SampleClass.php");
        $this->assertFileExists($filename);

        $this->assertFileContainsString($filename, "class SampleClass");

        $this->fs->rmdir("app/Test");
    }

    /** @test */
    public function should_create_command_using_user_template()
    {
        $class = "SampleCommand";

        $this->artisan("craft:command {$class} --template <project>/custom-templates/command.mustache")
            ->assertExitCode(0);

        $filename = $this->pathJoin("App", "Console", "Commands", "${class}.php");

        $this->assertFileExists($filename);

        $this->assertFileContainsString($filename, "class SampleCommand");
        $this->assertFileContainsString($filename, "// custom-command");

        $this->fs->rmdir("App/Console");
    }
}
