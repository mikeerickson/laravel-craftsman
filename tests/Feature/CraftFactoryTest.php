<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\TestHelpersTrait;
use App\CraftsmanFileSystem;

/**
 * Class CraftFactoryTest
 * @package Tests\Feature
 */
class CraftFactoryTest extends TestCase
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
    public function should_execute_craft_factory_command()
    {
        $model = "App/Models/Test";
        $model_path = "App\\Models\\Test";

        $this->artisan("craft:factory TestFactory --model {$model}")
            ->assertExitCode(0);

        $factoryPath = $this->fs->factory_path();
        $filename = $this->fs->path_join($factoryPath, "TestFactory.php");
        $this->assertFileExists($filename);

        $this->assertFileContainsString($filename, "use {$model_path};");

        $this->cleanUp();
    }

    /** @test */
    public function should_craft_factory_using_custom_template()
    {
        $model = "App/Models/Test";
        $model_path = "App\\Models\\Test";

        $this->artisan("craft:factory TestFactory --model {$model} --template <project>/templates/custom.mustache --overwrite")
            ->assertExitCode(0);

        $factoryPath = $this->fs->factory_path();
        $filename = $this->fs->path_join($factoryPath, "TestFactory.php");
        $this->assertFileExists($filename);

        $this->assertFileContainsString($filename, "testMethod");

        $this->cleanUp();
    }

    public function cleanUp()
    {
        $this->fs->rmdir("database");
    }
}
