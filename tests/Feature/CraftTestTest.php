<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\TestHelpersTrait;
use App\CraftsmanFileSystem;

/**
 * Class CraftTestTest
 * @package Tests\Feature
 */
class CraftTestTest extends TestCase
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
        //        $this->withoutExceptionHandling();
    }

    /**
     *
     */
    function tearDown(): void
    {
        parent::tearDown();
    }

    /** @test */
    public function should_create_simple_test_command()
    {
        $class = "ExampleFeatureTest";

        $this->artisan("craft:test {$class}")
            ->assertExitCode(0);

        $filename = $this->fs->path_join("tests", "Feature", "{$class}.php");

        $this->assertFileExists($filename);

        $this->assertFileContainsString($filename, "class ${class}");

        unlink($filename);
    }

    /** @test */
    public function should_create_unit_test_command()
    {
        $class = "ExampleUnitTest";

        $this->artisan("craft:test {$class} --unit")
            ->assertExitCode(0);

        $filename = $this->pathJoin("tests", "Unit", "{$class}.php");

        $this->assertFileExists($filename);

        $this->assertFileContainsString($filename, "class ${class}");
        $this->assertFileContainsString($filename, "namespace App\Unit;");

        unlink($filename);
    }

    /** @test */
    public function should_create_unit_test_with_setup_command()
    {
        $class = "ExampleUnitSetupTest";

        $this->artisan("craft:test {$class} --unit --setup")
            ->assertExitCode(0);

        $filename = $this->fs->path_join("tests", "Unit", "{$class}.php");

        $this->assertFileExists($filename);

        $data = file_get_contents($filename);
        $this->assertStringContainsString("function setUp()", $data);

        unlink($filename);
    }

    /** @test */
    public function should_create_unit_test_with_teardown_command()
    {
        $class = "ExampleUnitTeardownTest";

        $this->artisan("craft:test {$class} --unit --teardown")
            ->assertExitCode(0);

        $filename = $this->fs->path_join("tests", "Unit", "{$class}.php");

        $this->assertFileExists($filename);

        $data = file_get_contents($filename);
        $this->assertStringContainsString("function tearDown()", $data);

        unlink($filename);
    }
}
