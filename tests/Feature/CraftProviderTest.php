<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\TestHelpersTrait;
use App\CraftsmanFileSystem;

/**
 * Class CraftClassTest
 * @package Tests\Feature
 */
class CraftProviderTest extends TestCase
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
    public function should_create_simple_provider_command()
    {
        $class = "TestServiceProvider";

        $this->artisan("craft:provider {$class} --overwrite")
            ->assertExitCode(0);

        $filename = $this->pathJoin($this->fs->provider_path(), "${class}.php");

        $this->assertFileExists($filename);

        $this->assertFileContainsString($filename, "class {$class}");

        unlink($filename);
    }

    /** @test */
    public function should_return_error_when_filename_exists()
    {
        $class = "TestServiceProvider";

        $this->artisan("craft:provider {$class}");

        $this->artisan("craft:provider {$class}")
            ->assertExitCode(-1);

        $filename = $this->pathJoin($this->fs->provider_path(), "${class}.php");

        unlink($filename);
    }

    /** @test */
    public function should_create_provider_with_namespace()
    {
        $path = "App/MyProviders";
        $class = "TestProvider";

        $this->artisan("craft:provider ${path}/{$class} --overwrite")
            ->assertExitCode(0);

        $filename = $this->pathJoin($path, "${class}.php");

        $this->assertFileExists($filename);

        $this->assertFileContainsString($filename, "namespace App\\MyProviders;");
        $this->assertFileContainsString($filename, "class TestProvider");

        unlink($filename);

        $this->fs->rmdir("app/MyProviders");
    }

    /** @test */
    public function should_create_provider_using_user_template()
    {
        $class = "CustomTestProvider";

        $this->artisan("craft:provider {$class} --template <project>/custom-templates/provider.mustache --overwrite")
            ->assertExitCode(0);

        $filename = $this->pathJoin($this->fs->provider_path(), "${class}.php");
        $this->assertFileExists($filename);

        $this->assertFileContainsString($filename, "class ${class}");
        $this->assertFileContainsString($filename, "// custom-provider");

        unlink($filename);
    }

}
