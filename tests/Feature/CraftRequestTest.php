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
    public function setUp(): void
    {
        parent::setUp();

        $this->fs = new CraftsmanFileSystem();

        $this->withoutExceptionHandling();
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $this->fs->rmdir("app/Http");
    }

    /** @test */
    public function should_create_request_command()
    {
        $class = 'TestFormRequest';

        $this->artisan("craft:request {$class}")
            ->assertExitCode(0);

        $filename = $this->pathJoin('app', 'Http', 'Requests', 'TestFormRequest.php');
        $this->assertFileExists($filename);

        $this->assertFileContainsString($filename, "class {$class}");
    }

    /** @test */
    public function should_create_class_using_user_template()
    {
        $class = 'App/Http/Requests/TestFormRequest';

        $this->artisan("craft:request {$class} --template <project>/custom-templates/request.mustache")
            ->assertExitCode(0);

        $filename = $this->pathJoin('app', 'Http', 'Requests', 'TestFormRequest.php');
        $this->assertFileExists($filename);

        $this->assertFileContainsString($filename, 'class TestFormRequest');
        $this->assertFileContainsString($filename, 'public function rules()');
    }

    /** @test */
    public function should_return_error_when_filename_exists()
    {
        $class = 'TestFormRequest';

        $this->artisan("craft:request {$class}");

        $this->artisan("craft:request {$class}")
            ->assertExitCode(-1);

        $filename = $this->pathJoin($this->fs->request_path(), "${class}.php");

        unlink($filename);
    }

    /** @test */
    public function should_return_error_if_already_exists()
    {
        $class = 'TestFormRequest';

        $data = [
            'name' => $class,
        ];

        // create file twice to force error
        $this->fs->createFile('request', $class, $data);
        $result = $this->fs->createFile('request', $class, $data);

        $this->assertStringContainsString('already exists', $result['message']);

        // $this->assertFileExists($result);
        // $this->assertFileExists($result['filename']);
    }

    /** @test */
    public function should_create_resource_with_supplied_rules()
    {
        $class = 'TestFormRequest';

        $this->artisan("craft:request {$class} --rules title?required|unique:posts|max:255,body?required")
            ->assertExitCode(0);

        $filename = $this->pathJoin('app', 'Http', 'Requests', 'TestFormRequest.php');
        $this->assertFileExists($filename);

        $this->assertFileContainsString($filename, 'class TestFormRequest');

        $this->assertFileContainsString($filename, '"title" => "required|unique:posts|max:255"');
        $this->assertFileContainsString($filename, '"body" => "required"');
    }
}
