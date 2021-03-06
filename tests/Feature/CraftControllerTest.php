<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\TestHelpersTrait;
use App\CraftsmanFileSystem;

/**
 * Class CraftControllerTest
 * @package Tests\Feature
 */
class CraftControllerTest extends TestCase
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

    /**
     *
     */
    function tearDown(): void
    {
        parent::tearDown();

        $this->fs->rmdir("app/Http");
    }

    /** @test */
    public function should_create_empty_controller()
    {
        $class = "EmptyController";

        $this->artisan("craft:controller EmptyController")
            ->assertExitCode(0);

        $controllerPath = $this->fs->controller_path();
        $filename = $this->pathJoin($controllerPath, "{$class}.php");
        $this->assertFileExists($filename);

        $this->assertFileContainsString($filename, "class {$class}");
    }

    /** @test */
    public function should_create_invokable_controller()
    {
        $class = "InvokableController";

        $this->artisan("craft:controller ${class} --invokable")
            ->assertExitCode(0);

        $controllerPath = $this->fs->controller_path();
        $filename = $this->pathJoin($controllerPath, "{$class}.php");
        $this->assertFileExists($filename);

        $this->assertFileContainsString($filename, "public function __invoke");
    }

    /** @test */
    public function should_not_add_any_other_options_when_invokable_controller()
    {
        $class = "InvokableController";

        $this->artisan("craft:controller ${class} --invokable --model App/Models/Post --api")
            ->assertExitCode(0);

        $controllerPath = $this->fs->controller_path();
        $filename = $this->pathJoin($controllerPath, "{$class}.php");
        $this->assertFileExists($filename);

        $this->assertFileContainsString($filename, "public function __invoke");

        $this->assertFileNotContainString($filename, "Post");
    }

    /** @test */
    public function should_execute_craft_controller_command()
    {
        $model = "App/Models/Test";
        $model_path = "App\\Models\\Test";

        $this->artisan("craft:controller TestController --model {$model}")
            ->assertExitCode(0);

        $controllerPath = $this->fs->controller_path();
        $filename = $this->fs->path_join($controllerPath, "TestController.php");
        $this->assertFileExists($filename);

        $parts = explode("/", $model);
        $model_name = array_pop($parts);

        // spot check merged data
        $data = file_get_contents($filename);
        $this->assertStringContainsString("class {$model_name}", $data);
        $this->assertStringContainsString("public function index()", $data);
        $this->assertStringContainsString("use {$model_path};", $data);

        $this->assertStringContainsString("edit", $data);
        $this->assertStringContainsString("create", $data);
    }

    /** @test */
    public function should_create_api_controller()
    {
        $model = "App/Models/Contact";
        $model_path = "App\\Models\\Contact";

        $this->artisan("craft:controller Api/ContactAPIController --model {$model} --api")
            ->assertExitCode(0);

        $controllerPath = $this->fs->controller_path();
        $filename = $this->fs->path_join($controllerPath, "API", "ContactAPIController.php");
        $this->assertFileExists($filename);

        $parts = explode("/", $model);
        $model_name = array_pop($parts);

        // spot check merged data
        $data = file_get_contents($filename);

        $this->assertStringContainsString("class {$model_name}", $data);
        $this->assertStringContainsString("return {$model_name}::all();", $data);
        $this->assertStringContainsString("use {$model_path};", $data);

        $this->assertStringNotContainsString("edit", $data);
        $this->assertStringNotContainsString("create", $data);
    }

    /** @test */
    public function should_create_controller_using_custom_template()
    {
        $model = "App/Models/Contact";

        $this->artisan("craft:controller CustomController --model {$model} --template <project>/custom-templates/controller.mustache --overwrite")
            ->assertExitCode(0);

        $controllerPath = $this->fs->controller_path();
        $filename = $this->fs->path_join($controllerPath, "CustomController.php");
        $this->assertFileExists($filename);

        // spot check merged data
        $data = file_get_contents($filename);

        $this->assertStringContainsString("// custom-controller", $data);
    }

    /** @test */
    public function should_create_resource_controller()
    {
        $this->artisan("craft:controller ResourceController --resource --overwrite")
            ->assertExitCode(0);

        $controllerPath = $this->fs->controller_path();
        $filename = $this->fs->path_join($controllerPath, "ResourceController.php");
        $this->assertFileExists($filename);

        // spot check merged data
        $data = file_get_contents($filename);

        $this->assertStringContainsString("ResourceController", $data);
        $this->assertStringNotContainsString("use Illuminate\Http\Resources\Json\ResourceCollection;", $data);
    }

    /** @test */
    public function should_create_resource_controller_with_collection()
    {
        $this->artisan("craft:controller CustomResource --resource --collection --overwrite")
            ->assertExitCode(0);

        $resourcePath = $this->fs->controller_path();
        $filename = $this->fs->path_join($resourcePath, "CustomResource.php");
        $this->assertFileExists($filename);

        // spot check merged data
        $data = file_get_contents($filename);

        $this->assertStringContainsString("public function index()", $data);
        $this->assertStringContainsString("public function create()", $data);
        $this->assertStringContainsString("public function store(Request \$request)", $data);
        $this->assertStringContainsString("public function show(\$id)", $data);
        $this->assertStringContainsString("public function edit(\$id)", $data);
        $this->assertStringContainsString("public function update(Request \$request, \$id)", $data);
        $this->assertStringContainsString("public function destroy(\$id)", $data);
    }
}
