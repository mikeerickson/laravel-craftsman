<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Tests\TestCase;
use Tests\TestHelpersTrait;
use App\CraftsmanFileSystem;

/**
 * Class CraftsmanFileSystemTest
 * @package Tests\Unit
 */
class CraftsmanFileSystemTest extends TestCase
{
    use TestHelpersTrait;

    /**
     * @var CraftsmanFileSystem
     */
    protected $fs;

    /**
     * CraftsmanFileSystemTest constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->fs = new CraftsmanFileSystem();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        (new CraftsmanFileSystem())->rmdir("resources/views/coverage");
    }

    public function setUp(): void
    {
        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown(); // TODO: Change the autogenerated stub

        $this->fs->rmdir("database/migrations");
    }

    /** @test */
    public function should_return_correct_controller_path()
    {
        $result = $this->fs->path_join(app_path(), "Http", "Controllers");

        $path = $this->fs->controller_path("Controllers");

        $this->assertSame($result, $path);
    }

    /** @test */
    public function should_return_correct_resources_path()
    {
        $result = $this->fs->path_join(app_path(), "Http", "Resources");

        $path = $this->fs->resource_path("Resources");

        $this->assertSame($result, $path);
    }

    /** @test */
    public function should_return_correct_migration_path()
    {
        $result = $this->fs->path_join(base_path(), "database", "migrations");

        $path = $this->fs->migration_path("migrations");

        $this->assertSame($result, $path);
    }

    /** @test */
    public function should_return_correct_factory_path()
    {
        $result = $this->fs->path_join(database_path(), "factories");

        $path = $this->fs->factory_path();

        $this->assertSame($result, $path);
    }

    /** @test */
    public function should_return_correct_default_model_path()
    {
        $result = app_path();

        $path = $this->fs->model_path();

        $this->assertSame($result, $path);
    }

    /** @test */
    public function should_return_correct_custom_model_path()
    {
        $result = $this->fs->path_join(app_path(), "Models");

        $path = $this->fs->model_path("Models");

        $this->assertSame($result, $path);
    }

    /** @test */
    public function should_return_correct_request_path()
    {
        $result = $this->fs->path_join(app_path(), "Http", "Requests");

        $path = $this->fs->request_path();

        $this->assertSame($result, $path);
    }

    /** @test */
    public function should_return_correct_seed_path()
    {
        $result = $this->fs->path_join(database_path(), "seeds");

        $path = $this->fs->seed_path();

        $this->assertSame($result, $path);
    }

    /** @test */
    public function should_return_correct_view_path()
    {
        $result = $this->fs->path_join(resource_path(), "views");

        $path = $this->fs->view_path("views");

        $this->assertSame($result, $path);
    }

    /** @test */
    public function should_produce_error_when_template_not_found()
    {
        $filename = $this->fs->getTemplateFilename("<project>/templates/class-missing.mustache");

        $this->assertStringContainsString("templates/class-missing.mustache Not Found", $filename);
    }

    /** @test */
    public function should_return_custom_template_using_root()
    {
        $result = getcwd() . DIRECTORY_SEPARATOR . "templates/class.mustache";

        $filename = $this->fs->getTemplateFilename("<root>/templates/class.mustache");

        $this->assertSame($result, $filename);
    }

    /** @test */
    public function should_return_class_template_filename()
    {
        $result = getcwd() . DIRECTORY_SEPARATOR . "templates/class.mustache";

        $filename = $this->fs->getTemplateFilename("class");

        $this->assertSame($result, $filename);
    }

    /** @test */
    public function should_return_controller_template_filename()
    {
        $result = getcwd() . DIRECTORY_SEPARATOR . "templates/controller.mustache";

        $filename = $this->fs->getTemplateFilename("controller");

        $this->assertSame($result, $filename);
    }

    /** @test */
    public function should_return_api_controller_filename()
    {
        $result = getcwd() . DIRECTORY_SEPARATOR . "templates/api-controller.mustache";

        $filename = $this->fs->getTemplateFilename("api-controller");

        $this->assertSame($result, $filename);
    }

    /** @test */
    public function should_return_empty_controller_filename()
    {
        $result = getcwd() . DIRECTORY_SEPARATOR . "templates/empty-controller.mustache";

        $filename = $this->fs->getTemplateFilename("empty-controller");

        $this->assertSame($result, $filename);
    }

    /** @test */
    public function should_return_view_index_filename()
    {
        $result = getcwd() . DIRECTORY_SEPARATOR . "templates/view-index.mustache";

        $filename = $this->fs->getTemplateFilename("view-index");

        $this->assertSame($result, $filename);
    }

    /** @test */
    public function should_return_view_create_filename()
    {
        $result = getcwd() . DIRECTORY_SEPARATOR . "templates/view-create.mustache";

        $filename = $this->fs->getTemplateFilename("view-create");

        $this->assertSame($result, $filename);
    }

    /** @test */
    public function should_return_view_show_filename()
    {
        $result = getcwd() . DIRECTORY_SEPARATOR . "templates/view-show.mustache";

        $filename = $this->fs->getTemplateFilename("view-show");

        $this->assertSame($result, $filename);
    }

    /** @test */
    public function should_return_view_edit_filename()
    {
        $result = getcwd() . DIRECTORY_SEPARATOR . "templates/view-edit.mustache";

        $filename = $this->fs->getTemplateFilename("view-edit");

        $this->assertSame($result, $filename);
    }


    /** @test */
    public function should_tests_filename()
    {
        $result = getcwd() . DIRECTORY_SEPARATOR . "templates/test.mustache";

        $filename = $this->fs->getTemplateFilename("test");

        $this->assertSame($result, $filename);
    }

    /** @test */
    public function should_return_model_filename()
    {
        $result = getcwd() . DIRECTORY_SEPARATOR . "templates/model.mustache";

        $filename = $this->fs->getTemplateFilename("model");

        $this->assertSame($result, $filename);
    }

    /** @test */
    public function should_return_migration_filename()
    {
        $result = getcwd() . DIRECTORY_SEPARATOR . "templates/migration.mustache";

        $filename = $this->fs->getTemplateFilename("migration");

        $this->assertSame($result, $filename);
    }

    /** @test */
    public function should_return_factories_filename()
    {
        $result = getcwd() . DIRECTORY_SEPARATOR . "templates/factory.mustache";

        $filename = $this->fs->getTemplateFilename("factory");

        $this->assertSame($result, $filename);
    }

    /** @test */
    public function should_return_phar_path()
    {
        $path = $this->fs->getPharPath();

        $this->assertSame("", $path);
    }

    /** @test */
    public function should_return_seeds_filename()
    {
        $result = getcwd() . DIRECTORY_SEPARATOR . "templates/seed.mustache";

        $filename = $this->fs->getTemplateFilename("seed");

        $this->assertSame($result, $filename);
    }

    /** @test */
    public function should_return_last_migration_filename()
    {
        $migrationName = "create_customers_file";
        $data = [
            "model" => "App/Models/Customer",
        ];

        $result = $this->fs->createFile("migration", $migrationName, $data);

        $filename = $this->fs->tildify($this->fs->getLastFilename("database/migrations", $migrationName));

        $this->assertSame($result["filename"], $filename);

        $this->fs->rmdir("database/migrations");
    }

    /** @test */
    public function should_return_views_output_path()
    {
        $result = $this->fs->path_join(base_path(), "resources", "views");

        $path = $this->fs->getOutputPath("views");

        $this->assertSame($result, $path);
    }

    /** @test */
    public function should_return_class_output_path()
    {
        $path = $this->fs->getOutputPath("class");

        $this->assertSame("app", $path);
    }

    /** @test */
    public function should_return_controller_output_path()
    {
        $result = $this->fs->path_join(app_path(), "Http", "Controllers");

        $path = $this->fs->getOutputPath("controller");

        $this->assertSame($result, $path);
    }

    /** @test */
    public function should_return_migrations_output_path()
    {

        $result = $this->fs->path_join(database_path(), "migrations");

        $path = $this->fs->getOutputPath("migrations");

        $this->assertSame($result, $path);
    }

    /** @test */
    public function should_return_models_output_path()
    {
        $result = $this->fs->path_join(app_path());

        $path = $this->fs->getOutputPath("model");

        $this->assertSame($result, $path);
    }

    /** @test */
    public function should_return_seeds_output_path()
    {
        $result = $this->fs->path_join(database_path(), "seeds");

        $path = $this->fs->getOutputPath("seed");

        $this->assertSame($result, $path);
    }

    /** @test */
    public function should_return_factories_output_path()
    {
        $result = $this->fs->path_join(database_path(), "factories");

        $path = $this->fs->getOutputPath("factory");

        $this->assertSame($result, $path);
    }

    /** @test */
    public function should_return_tests_output_path()
    {
        $result = $this->fs->path_join(base_path(), "tests");

        $path = $this->fs->getOutputPath("test");

        $this->assertSame($result, $path);
    }

    /** @test */
    public function should_call_path_join()
    {
        $filename = $this->fs->path_join("App", "Models", "Test.php");

        $this->assertSame("App/Models/Test.php", $filename);
    }

    /** @test */
    public function should_call_pathjoin()
    {
        $filename = $this->fs->pathJoin("App", "Models", "Test.php");

        $this->assertSame("App/Models/Test.php", $filename);
    }

    /** @test */
    public function should_call_create_view_create()
    {
        $filename = "resources/views/coverage/create.blade.php";

        $data = $this->getDefaultViewOptions(["noCreate" => false]);

        $filenames = $this->fs->createViewFiles("coverage", $data);

        $this->assertContains("create.blade.php", $filenames);

        unlink($filename);
    }

    /**
     * @param $overrides
     * @return array
     */
    public function getDefaultViewOptions($overrides)
    {
        return array_merge([
            "noCreate" => true,
            "noEdit" => true,
            "noIndex" => true,
            "noShow" => true,
            "extends" => "",
            "section" => "",
            "overwrite" => true,
        ], $overrides);
    }

    /** @test */
    public function should_call_create_view_edit()
    {
        $filename = "resources/views/coverage/edit.blade.php";

        $data = $this->getDefaultViewOptions(["noEdit" => false]);

        $filenames = $this->fs->createViewFiles("coverage", $data);

        $this->assertContains("edit.blade.php", $filenames);

        unlink($filename);
    }

    /** @test */
    public function should_call_create_view_index()
    {
        $filename = "resources/views/coverage/index.blade.php";

        $data = $this->getDefaultViewOptions(["noIndex" => false]);

        $filenames = $this->fs->createViewFiles("coverage", $data);

        $this->assertContains("index.blade.php", $filenames);

        unlink($filename);
    }

    /** @test */
    public function should_call_create_view_show()
    {
        $filename = "resources/views/coverage/show.blade.php";

        $data = $this->getDefaultViewOptions(["noShow" => false]);

        $filenames = $this->fs->createViewFiles("coverage", $data);

        $this->assertContains("show.blade.php", $filenames);

        unlink($filename);
    }

    /** @test */
    public function should_call_create_view_with_extends_show()
    {
        $filename = "resources/views/coverage/show.blade.php";

        $data = $this->getDefaultViewOptions(["noShow" => false, "extends" => "partials.master"]);

        $filenames = $this->fs->createViewFiles("coverage", $data);

        $this->assertContains("show.blade.php", $filenames);

        $this->assertFileContainsString($filename, "@extends");

        unlink($filename);
    }

    /** @test */
    public function should_call_create_view_with_content_show()
    {
        $filename = "resources/views/coverage/show.blade.php";

        $data = $this->getDefaultViewOptions(["noShow" => false, "section" => "content"]);

        $filenames = $this->fs->createViewFiles("coverage", $data);

        $this->assertContains("show.blade.php", $filenames);

        $this->assertFileContainsString($filename, "@section");

        unlink($filename);
    }

    /** @test */
    public function should_build_complex_field_data()
    {
        $migrationName = "create_test_migration";
        $dt = Carbon::now()->format('Y_m_d_His');
        $migrationFilename = $dt . "_" . $migrationName;

        $fields = "first_name:string@20:nullable, last_name:string@60:nullable, email:string@80:nullable:unique";

        $data = [
            "model" => "App/Models/Test",
            "tablename" => "tests",
            "fields" => $fields,
        ];

        $this->fs->createFile("migration", $migrationFilename, $data);

        $lastFilename = $this->fs->getLastFilename("database/migrations", $migrationName);

        $this->assertFileExists($lastFilename);

        $this->assertFileContainsString($lastFilename, "\$table->string('first_name',20)->nullable();");

        unlink($lastFilename);
    }

    /** @test */
    public function should_get_build()
    {
        $result = get_build();

        $this->assertTrue((int) $result > 0);
    }

    /*
     * View Option Factory
     */

    /** @test */
    public function should_load_app_config_version_build()
    {
        $result = include "./config/app.php";
        $this->assertArrayHasKey("version", $result);
    }

    /** @test */
    public function should_get_version()
    {
        $result = get_version();

        $parts = explode(".", $result);

        $this->assertTrue(count($parts) === 3);
    }

    /** @test  */
    public function it_should_use_custom_configuration_access(): void
    {
        $configValue = $this->fs->getConfigValue("templates.model");
        $this->AssertEquals("templates/model.mustache", $configValue);
    }

    /** @test  */
    public function it_should_use_custom_configuration_value(): void
    {
        $configValue = $this->fs->getConfigValue("migrations.useCurrentDefault");
        $this->AssertEquals(true, $configValue);
    }
}
