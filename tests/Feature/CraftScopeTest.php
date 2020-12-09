<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\TestHelpersTrait;
use App\CraftsmanFileSystem;

/**
 * Class CraftClassTest
 * @package Tests\Feature
 */
class CraftScopeTest extends TestCase
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
    public function should_create_simple_scope_command()
    {
        $class = "TestScope";

        $this->artisan("craft:scope {$class} --overwrite")
            ->assertExitCode(0);

        $filename = $this->pathJoin("app/scopes", "{$class}.php");
        $this->assertFileExists($filename);

        $this->assertFileContainsString($filename, "class {$class}");

        $this->fs->delete($filename);
    }

    /** @test */
    public function should_create_scope_class_using_user_template()
    {
        $class = "TestScope";

        $this->artisan("craft:scope {$class} --template <project>/custom-templates/scope.mustache")
            ->assertExitCode(0);

        $filename = $this->pathJoin("app/Scopes", "{$class}.php");
        $this->assertFileExists($filename);

        $this->assertFileContainsString($filename, "class {$class}");
        $this->assertFileContainsString($filename, "public function apply");

        $this->fs->rmdir("app/Scopes");
    }

    /** ------------------------------------------------------------------------------------------------------
     * Test Helpers
     * ------------------------------------------------------------------------------------------------------- */
}
