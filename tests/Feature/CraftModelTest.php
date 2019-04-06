<?php

namespace Tests\Feature;

use App\CraftsmanFileSystem;
use Tests\TestCase;
use Tests\TestHelpersTrait;

/**
 * Class CraftModelTest
 * @package Tests\Feature
 */
class CraftModelTest extends TestCase
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
    public function should_execute_default_craft_model_command()
    {
        $model = "Post";

        $this->artisan("craft:model {$model}")
            ->assertExitCode(0);

        $modelPath = $this->fs->model_path();
        $filename = $this->pathJoin($modelPath, "{$model}.php");

        $this->assertFileContainsString($filename, "class {$model} extends Model");

        unlink("app/{$model}.php");
    }

    /** @test */
    public function should_execute_custom_craft_model_command()
    {
        $model = "Post";

        $this->artisan('craft:model App/Models/Post')
            ->assertExitCode(0);

        $modelPath = $this->fs->model_path();
        $filename = $this->pathJoin($modelPath, "Models", "{$model}.php");

        $this->assertFileContainsString($filename, "class {$model} extends Model");

        $this->fs->rmdir("app/Models");
    }
}
