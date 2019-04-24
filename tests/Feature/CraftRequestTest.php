<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\TestHelpersTrait;
use App\CraftsmanFileSystem;

/**
 * Class CraftClassTest
 * @package Tests\Feature
 */
class CraftRequestTest extends TestCase
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
    public function should_create_request_command()
    {
        $class = "TestFormRequest";

        $this->artisan("craft:request {$class}")
            ->assertExitCode(0);

        $filename = $this->pathJoin("app", "Http", "Requests", "TestFormRequest.php");
        $this->assertFileExists($filename);

        $this->assertFileContainsString($filename, "class {$class}");

        unlink($filename);
    }

    /** @test */
    public function should_create_class_using_user_template()
    {
        $class = "App/Http/Requests/TestFormRequest";

        $this->artisan("craft:request {$class} --template <project>/templates/custom-request.mustache")
            ->assertExitCode(0);

        $filename = $this->pathJoin("app", "Http", "Requests", "TestFormRequest.php");
        $this->assertFileExists($filename);

        $this->assertFileContainsString($filename, "class TestFormRequest");
        $this->assertFileContainsString($filename, "public function rules()");

        $this->fs->rmdir("app/Http/Requests");
    }

    /** @test */
    public function should_return_error_if_already_exists()
    {
        $this->markTestIncomplete("Need to write this test");

    }

    /** @test */
    public function should_create_resource_with_supplied_rules()
    {
        $this->markTestIncomplete("Need to write this test");

    }

}
