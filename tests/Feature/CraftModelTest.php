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

        $this->cleanUp($filename);
    }

    /** @test */
    public function should_create_model_and_migration()
    {
        $migrationName = "create_tests_table";

        $this->artisan("craft:model App/Models/Test --migration --overwrite")
            ->assertExitCode(0);

        $filename = $this->fs->getLastMigrationFilename("database/migrations", $migrationName);
        $this->assertFileExists($filename);

        $this->assertFileContainsString($filename, "Schema::create('tests', function (Blueprint \$table) {");

        $this->cleanUp();
    }

    /** @test */
    public function should_execute_custom_craft_model_command()
    {
        $model = "Post";

        $this->artisan('craft:model App/Models/Post --table tests --factory')
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

        $this->artisan('craft:model App/Models/Post --template <project>/custom-templates/model.mustache --overwrite')
            ->assertExitCode(0);

        $modelPath = $this->fs->model_path("Models");
        $filename = $this->pathJoin($modelPath, "{$model}.php");

        $this->assertFileContainsString($filename, "class {$model} extends Model");

        unlink("{$modelPath}/{$model}.php");

        $this->cleanUp();
    }

    /** @test */
    public function should_create_all_assets_when_creating_model(): void
    {
        $model = "Customer";

        Artisan::call("craft:model App/Models/{$model} --table customers --all --overwrite");

        // verify model
        $modelPath = $this->fs->model_path("Models");
        $filename = $this->pathJoin($modelPath, "{$model}.php");
        $this->assertFileExists($filename);

        // verify factory
        $factoryPath = $this->fs->factory_path();
        $filename = $this->pathJoin($factoryPath, "{$model}Factory.php");
        $this->assertFileExists($filename);

        // verify controller
        $resourcePath = $this->fs->controller_path();
        $filename = $this->pathJoin($resourcePath, "{$model}Controller.php");
        $this->assertFileExists($filename);

        unlink("{$modelPath}/{$model}.php");

        $this->cleanUp();
    }

    /** @test */
    public function should_create_controller_when_controller_option_supplied(): void
    {
        $model = "Customer";

        Artisan::call("craft:model App/Models/{$model} --table customers --controller --overwrite");

        // verify model
        $controllerPath = $this->fs->controller_path();
        $filename = $this->pathJoin($controllerPath, "{$model}Controller.php");
        $this->assertFileExists($filename);

        $this->assertTrue(true);

        unlink($filename);

        $this->cleanUp();
    }

    /** @test */
    public function should_create_seeder_when_seed_option_supplied(): void
    {
        $model = "Customer";

        Artisan::call("craft:model {$model} --table customers --seed --overwrite");

        $seederPath = $this->fs->seed_path();

        $filename = $this->pathJoin($seederPath, "{$model}sTableSeeder.php");
        $this->assertFileExists($filename);

        $this->cleanUp();
    }

    /** @test */
    public function should_create_seeder_when_seed_option_supplied_custom_model_path(): void
    {
        $model = "Customer";

        Artisan::call("craft:model App/Models/{$model} --table customers --seed --overwrite");

        // verify model
        $seederPath = $this->fs->seed_path();

        $filename = $this->pathJoin($seederPath, "{$model}sTableSeeder.php");
        $this->assertFileExists($filename);

        $this->cleanUp();
    }

    /** ------------------------------------------------------------------------------------------------------
     * Test Helpers
     * ------------------------------------------------------------------------------------------------------- */

    /**
     * @param  string  $filename
     */
    private function cleanUp($filename = "")
    {
        $this->fs->delete($filename);
        $this->fs->rmdir("app/Models");
        $this->fs->rmdir("database");
    }

}
