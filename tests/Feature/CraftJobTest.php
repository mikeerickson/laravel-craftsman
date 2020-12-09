<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\TestHelpersTrait;
use App\CraftsmanFileSystem;

/**
 * Class CraftClassTest
 * @package Tests\Feature
 */
class CraftJobTest extends TestCase
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
    public function should_create_simple_job_command()
    {
        $class = "TestJob";

        $this->artisan("craft:job {$class} --overwrite")
            ->assertExitCode(0);

        $filename = $this->pathJoin("app", "jobs", "{$class}.php");
        $this->assertFileExists($filename);

        $this->assertFileContainsString($filename, "class {$class}");

        $this->fs->delete($filename);
    }

    /** @test */
    public function should_create_sync_job_command()
    {
        $class = "TestJob";

        $this->artisan("craft:job {$class} --sync --overwrite")
            ->assertExitCode(0);

        $filename = $this->pathJoin("app", "jobs", "{$class}.php");
        $this->assertFileExists($filename);

        $this->assertFileNotContainsString($filename, "InteractsWithQueue");
        $this->assertFileNotContainsString($filename, "Queueable");
        $this->assertFileNotContainsString($filename, "Queueable");

        $this->fs->delete($filename);
    }

    /** @test */
    public function should_create_job_jest_command()
    {
        $class = "TestJob";

        $this->artisan("craft:job {$class} --test --overwrite")
            ->assertExitCode(0);

        $filename = $this->pathJoin("app", "unit", "{$class}.php");
        $testFilename = $this->pathJoin("tests", "unit", "{$class}Test.php");
        $this->assertFileExists($testFilename);

        $this->fs->delete($filename);
        $this->fs->delete($testFilename);
    }
}
