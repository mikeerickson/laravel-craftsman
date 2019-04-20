<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\TestHelpersTrait;
use App\CraftsmanFileSystem;

/**
 * Class CraftViewsTest
 * @package Tests\Feature
 */
class CraftViewsTest extends TestCase
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
    public function should_craft_create_view()
    {
        $resource = "contacts";

        $this->artisan("craft:views {$resource} --extends partials.master --section content --no-index --no-edit --no-show")
            ->assertExitCode(0);

        $filename = $this->pathJoin("resources", "views", $resource, "create.blade.php");

        $this->assertFileExists($filename);

        $this->assertFileContainsString($filename, "View Create");
        $this->assertFileContainsString($filename, "@section('content')");

        $this->fs->rmdir("resources/views/{$resource}");
    }

    /** @test */
    public function should_craft_edit_view()
    {
        $resource = "contacts";

        $this->artisan("craft:views {$resource} --extends partials.master --section content --no-index --no-create --no-show")
            ->assertExitCode(0);

        $filename = $this->fs->path_join("resources", "views", $resource, "edit.blade.php");

        $this->assertFileExists($filename);

        $this->assertFileContainsString($filename, "View Edit");
        $this->assertFileContainsString($filename, "@section('content')");

        $this->fs->rmdir("resources/views/{$resource}");
    }

    /** @test */
    public function should_craft_index_view()
    {
        $resource = "contacts";

        $this->artisan("craft:views {$resource} --no-edit --no-create --no-show")
            ->assertExitCode(0);

        $filename = $this->fs->path_join("resources", "views", $resource, "index.blade.php");

        $this->assertFileExists($filename);

        $this->assertFileContainsString($filename, "View Index");

        $this->assertFileNotContainString($filename, "@section");
        $this->assertFileNotContainString($filename, "@extends");

        $this->fs->rmdir("resources/views/{$resource}");

    }

    /** @test */
    public function should_craft_show_view()
    {
        $resource = "contacts";

        $this->artisan("craft:views {$resource} --extends partials.master --no-index --no-create --no-edit")
            ->assertExitCode(0);

        $filename = $this->pathJoin("resources", "views", $resource, "show.blade.php");

        $this->assertFileExists($filename);

        $this->assertFileContainsString($filename, "View Show");
        $this->assertFileContainsString($filename, "@extends('partials.master')");
        $this->assertFileNotContainString($filename, "@section('content')");

        $this->fs->rmdir("resources/views/{$resource}");

    }

    /** @test */
    public function should_create_all_views()
    {
        $resource = "customers";

        $this->artisan("craft:views {$resource}")
            ->assertExitCode(0);

        $indexFilename = $this->fs->path_join("resources", "views", $resource, "index.blade.php");
        $createFilename = $this->fs->path_join("resources", "views", $resource, "create.blade.php");
        $editFilename = $this->fs->path_join("resources", "views", $resource, "edit.blade.php");
        $showFilename = $this->fs->path_join("resources", "views", $resource, "show.blade.php");

        $this->assertFileExists($createFilename);
        $this->assertFileExists($indexFilename);
        $this->assertFileExists($editFilename);
        $this->assertFileExists($showFilename);

        $this->fs->rmdir("resources/views/{$resource}");

    }

}
