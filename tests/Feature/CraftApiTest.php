<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\TestHelpersTrait;
use App\CraftsmanFileSystem;

/**
 * Class CraftClassTest
 * @package Tests\Feature
 */
class CraftApiTest extends TestCase
{
    use TestHelpersTrait;

    protected $fs;

    public function setUp(): void
    {
        parent::setUp();

        $this->fs = new CraftsmanFileSystem();

        $this->withoutExceptionHandling();
    }

    /** @test */
    public function should_craft_all_api_assets_using_default()
    {
        $resource = 'Contact';
        $model = "Contact";

        $this->artisan("craft:api {$resource} --model {$model} --overwrite")
            ->assertExitCode(0);

        // create model
        $filename = $this->fs->path_join($this->fs->model_path(), "{$resource}.php");
        $this->assertFileExists($filename);
        unlink($filename);

        // create controller
        $filename = $this->fs->path_join($this->fs->controller_path(), "API", "Api{$resource}Controller.php");
        $this->assertFileExists($filename);
        unlink($filename);

        // create factory
        $filename = $this->fs->path_join($this->fs->factory_path(), "{$resource}Factory.php");
        $this->assertFileExists($filename);
        unlink($filename);

        // create migration
        $migrationName = "create_contacts_table";
        $this->assertMigrationFileExists($migrationName);

        $this->cleanUp();
    }

    public function cleanUp()
    {
        $this->fs->rmdir("app/Http");
        $this->fs->rmdir("app/Models");
        $this->fs->rmdir("app/Test");
        $this->fs->rmdir("resources/views/posts");
        $this->fs->rmdir("database/migrations");
        $this->fs->rmdir("database/factories");
        $this->fs->rmdir("database/seeds");
    }

    /** @test */
    public function should_create_all_api_assets_using_custom_model_path(): void
    {
        $resource = "Contact";
        $migrationName = "create_contacts_table";

        $this->artisan("craft:api {$resource} --model App/Models/{$resource}")
            ->assertExitCode(0);

        // create model
        $filename = $this->fs->path_join($this->fs->model_path(), "Models", "{$resource}.php");
        $this->assertFileExists($filename);
        unlink($filename);

        // check controller
        $controllerFilename = $this->fs->path_join($this->fs->controller_path(), "API", "Api{$resource}Controller.php");
        $this->assertFileExists($controllerFilename);

        $factoryFilename = $this->fs->path_join($this->fs->factory_path(), "{$resource}Factory.php");
        $this->assertFileExists($factoryFilename);
        unlink($factoryFilename);

        $migrationFilename = $this->fs->getLastMigrationFilename("database/migrations", $migrationName);
        $this->assertFileExists($migrationFilename);
        unlink($migrationFilename);

        $this->cleanUp();
    }

    /** @test */
    public function should_use_custom_tablename()
    {
        $resource = "Contact";

        $this->artisan("craft:api {$resource} --model Contact --tablename contacters --overwrite")
            ->assertExitCode(0);

        $modelFilename = $this->fs->path_join($this->fs->model_path(), "{$resource}.php");
        $this->assertFileContainsString($modelFilename, "contacters");
        unlink($modelFilename);

        $this->assertTrue(true);

        $this->cleanUp();
    }

    /** @test */
    public function should_not_create_marked_resources(): void
    {
        $resource = 'Contact';

        $this->artisan("craft:api {$resource} --model {$resource} --no-model --no-controller --no-factory --no-migration --no-seed")
            ->assertExitCode(0);

        // check controller
        $filename = $this->fs->path_join($this->fs->controller_path(), "{$resource}Controller.php");
        $this->assertFileNotExists($filename);

        // check factory
        $filename = $this->fs->path_join($this->fs->factory_path(), "{$resource}Factory.php");
        $this->assertFalse(file_exists($filename));

        // check migration
        $migrationName = "create_contacts_table";
        $migrationFilename = $this->fs->getLastMigrationFilename("database/migrations", $migrationName);
        $this->assertNull($migrationFilename);

        // check seed
        $filename = $this->fs->path_join($this->fs->seed_path(), "{$resource}TableSeeder.php");
        $this->assertFalse(file_exists($filename));

        $this->cleanUp();
    }
}
