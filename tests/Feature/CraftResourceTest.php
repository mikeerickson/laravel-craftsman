<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\TestHelpersTrait;
use App\CraftsmanFileSystem;

/**
 * Class CraftControllerTest
 * @package Tests\Feature
 */
class CraftResourceTest extends TestCase
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
    public function should_create_resource_controller()
    {
        $this->artisan("craft:resource CustomResource --overwrite")
            ->assertExitCode(0);

        $controllerPath = $this->fs->resource_path();
        $filename = $this->fs->path_join($controllerPath, "CustomResource.php");
        $this->assertFileExists($filename);

        // spot check merged data
        $data = file_get_contents($filename);

        $this->assertStringContainsString("CustomResource", $data);
        $this->assertStringNotContainsString("use Illuminate\Http\Resources\Json\ResourceCollection;", $data);

        $this->fs->rmdir("app/Http/Resources");
    }

    /** @test */
    public function should_create_resource_controller_with_collection()
    {
        $this->artisan("craft:resource CustomResource --collection --overwrite")
            ->assertExitCode(0);

        $controllerPath = $this->fs->resource_path();
        $filename = $this->fs->path_join($controllerPath, "CustomResource.php");
        $this->assertFileExists($filename);

        // spot check merged data
        $data = file_get_contents($filename);

        $this->assertStringContainsString("use Illuminate\Http\Resources\Json\ResourceCollection;", $data);

        $this->fs->rmdir("app/Http/Resources");
    }
}
