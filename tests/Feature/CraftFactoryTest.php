<?php

namespace Tests\Feature;

use App\CraftsmanFileSystem;
use Tests\TestCase;
use Tests\TestHelpersTrait;

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

        $this->fs->rmdir("database/factories");
    }
}
