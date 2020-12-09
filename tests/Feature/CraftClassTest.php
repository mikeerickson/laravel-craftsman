<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\TestHelpersTrait;
use App\CraftsmanFileSystem;

/**
 * Class CraftClassTest
 * @package Tests\Feature
 */
class CraftClassTest extends TestCase
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
    public function should_create_simple_class_command()
    {
        $class = "Test";

        $this->artisan("craft:class {$class}")
            ->assertExitCode(0);

        $filename = $this->pathJoin("app", "Test.php");
        $this->assertFileExists($filename);

        $this->assertFileContainsString($filename, "class {$class}");

        $this->fs->delete($filename);
    }

    /** @test */
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

    /** @test */
    public function should_create_class_using_user_template()
    {
        $class = "App/Test/SampleClass";

        $this->artisan("craft:class {$class} --template <project>/custom-templates/class.mustache")
            ->assertExitCode(0);

        $filename = $this->pathJoin("app/Test", "SampleClass.php");
        $this->assertFileExists($filename);

        $this->assertFileContainsString($filename, "class SampleClass");
        $this->assertFileContainsString($filename, "testMethod");

        $this->fs->rmdir("app/Test");
    }

    /** ------------------------------------------------------------------------------------------------------
     * Test Helpers
     * ------------------------------------------------------------------------------------------------------- */
}
