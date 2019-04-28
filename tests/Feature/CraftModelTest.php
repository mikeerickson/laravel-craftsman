<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\TestHelpersTrait;
use App\CraftsmanFileSystem;
use Illuminate\Support\Facades\Artisan;

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

        unlink($filename);
    }

    /** @test */
    public function should_execute_custom_craft_model_command()
    {
        $model = "Post";

        $this->artisan('craft:model App/Models/Post')
            ->assertExitCode(0);

        $modelPath = $this->fs->model_path("Models");
        $filename = $this->pathJoin($modelPath, "{$model}.php");

        $this->assertFileContainsString($filename, "class {$model} extends Model");

        unlink("{$modelPath}/{$model}.php");
    }

    /** @test */
    public function should_craft_model_using_custom_template()
    {
        $model = "Post";

        $this->artisan('craft:model App/Models/Post --template <project>/templates/custom.mustache --overwrite')
            ->assertExitCode(0);

        $modelPath = $this->fs->model_path("Models");
        $filename = $this->pathJoin($modelPath, "{$model}.php");

        $this->assertFileContainsString($filename, "class {$model} extends Model");

        unlink("{$modelPath}/{$model}.php");
        $this->fs->rmdir("app/Models");
    }

    /** @test  */
    public function should_create_all_assets_when_creating_model(): void
    {
        $model = "Customer";

        Artisan::call("craft:model App/Models/{$model} --tablename customers --all --overwrite");

        // verify model
        $modelPath = $this->fs->model_path("Models");
        $filename = $this->pathJoin($modelPath, "{$model}.php");
        $this->assertFileExists($filename);

        // verify factory
        $factoryPath = $this->fs->factory_path();
        $filename = $this->pathJoin($factoryPath, "{$model}Factory.php");
        $this->assertFileExists($filename);

        // verify resource
        $resourcePath = $this->fs->resource_path();
        $filename = $this->pathJoin($resourcePath, "{$model}Resource.php");
        $this->assertFileExists($filename);

        unlink("{$modelPath}/{$model}.php");
        $this->fs->rmdir("database/factories");
    }
}
