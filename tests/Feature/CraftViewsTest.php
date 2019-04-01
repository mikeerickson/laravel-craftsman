<?php

namespace Tests\Feature;

use App\CraftsmanFileSystem;
use Tests\TestCase;

class CraftViewsTest extends TestCase
{
    protected $fs;

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

        $createFilename = $this->fs->path_join("resources", "views", $resource, "create.blade.php");

        $this->assertFileExists($createFilename);
    }

    /** @test */
    public function should_craft_edit_view()
    {
        $resource = "contacts";

        $this->artisan("craft:views {$resource} --extends partials.master --section content --no-index --no-create --no-show")
            ->assertExitCode(0);

        $createFilename = $this->fs->path_join("resources", "views", $resource, "edit.blade.php");

        $this->assertFileExists($createFilename);

    }

    /** @test */
    public function should_craft_index_view()
    {
        $resource = "contacts";

        $this->artisan("craft:views {$resource} --extends partials.master --section content --no-edit --no-create --no-show")
            ->assertExitCode(0);

        $createFilename = $this->fs->path_join("resources", "views", $resource, "index.blade.php");

        $this->assertFileExists($createFilename);

    }

    /** @test */
    public function should_craft_show_view()
    {
        $resource = "contacts";

        $this->artisan("craft:views {$resource} --extends partials.master --section content --no-index --no-create --no-edit")
            ->assertExitCode(0);

        $createFilename = $this->fs->path_join("resources", "views", $resource, "show.blade.php");

        $this->assertFileExists($createFilename);

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

//        $data = file_get_contents($filename);
//        $this->assertStringContainsString("use {$model_path};", $data);
    }
}
