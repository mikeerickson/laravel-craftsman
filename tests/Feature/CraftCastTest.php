<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\TestHelpersTrait;
use App\CraftsmanFileSystem;

/**
 * Class CraftClassTest
 * @package Tests\Feature
 */
class CraftCastTest extends TestCase
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
    public function should_create_simple_cast_command()
    {
        $class = "TestCast";

        $this->artisan("craft:cast {$class} --overwrite")
            ->assertExitCode(0);

        $filename = $this->pathJoin("app", "casts", "{$class}.php");
        $this->assertFileExists($filename);

        $this->assertFileContainsString($filename, "class {$class}");

        $this->fs->delete($filename);
    }
}
