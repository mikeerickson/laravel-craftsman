<?php

namespace Tests\Unit;

use App\CraftsmanFileSystem;
use Tests\TestCase;

class CraftsmanFileSystemTest extends TestCase
{

    protected $fs;

    public function setUp(): void
    {
        parent::setUp();

        $this->fs = new CraftsmanFileSystem();
    }

    /** @test */
    public function should_return_correct_controller_path()
    {
        // store models in Models directory in app directory
        $result = path_join(app_path(), "Controllers");

        $path = $this->fs->model_path("Controllers");

        $this->assertSame($result, $path);
    }

    /** @test */
    public function should_return_correct_migration_path()
    {
        // store models in Models directory in app directory
        $result = path_join(app_path(), "migrations");

        $path = $this->fs->model_path("migrations");

        $this->assertSame($result, $path);
    }

    /** @test */
    public function should_return_correct_factory_path()
    {
        // store models in Models directory in app directory
        $result = path_join(app_path(), "factory");

        $path = $this->fs->model_path("factory");

        $this->assertSame($result, $path);
    }

    /** @test */
    public function should_return_correct_default_model_path()
    {
        // store models in Models directory in app directory
        $result = basename(app_path());

        $path = $this->fs->model_path();

        $this->assertSame($result, $path);
    }

    /** @test */
    public function should_return_correct_custom_model_path()
    {
        // store models in Models directory in app directory
        $result = path_join(app_path(), "Models");

        $path = $this->fs->model_path("Models");

        $this->assertSame($result, $path);
    }

    /** @test */
    public function should_return_correct_seed_path()
    {
        // store models in Models directory in app directory
        $result = path_join(app_path(), "seeds");

        $path = $this->fs->model_path("seeds");

        $this->assertSame($result, $path);
    }

    /** @test */
    public function should_return_correct_view_path()
    {
        // store models in Models directory in app directory
        $result = path_join(app_path(), "views");

        $path = $this->fs->model_path("views");

        $this->assertSame($result, $path);
    }

    /** @test */
    public function should_return_last_migration_filename()
    {
        $this->markTestIncomplete();
    }
}
