<?php

namespace Tests\Feature;

use App\CraftsmanFileSystem;
use Tests\TestCase;
use Tests\TestHelpersTrait;

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

        unlink($filename);
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
}
