<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\TestHelpersTrait;
use App\CraftsmanFileSystem;

/**
 * Class CraftControllerTest
 * @package Tests\Feature
 */
class CraftRouteModelBindingTest extends TestCase
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
    public function should_create_route_model_binding_controller()
    {
        $class = "PostRouteController";

        $this->artisan("craft:controller {$class} --binding --model App/Models/Post --overwrite")
            ->assertExitCode(0);

        $controllerPath = $this->fs->controller_path();
        $filename = $this->pathJoin($controllerPath, "{$class}.php");
        $this->assertFileExists($filename);

        $this->assertFileContainsString($filename, "class {$class}");
    }
}
