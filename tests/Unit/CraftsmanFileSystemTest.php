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
    /**
     * A basic test example.
     *
     * @return void
     */

    /** @test */
    public function should_return_correct_controller_path()
    {
        $result = path_join(app_path(), "Http", "Controllers");
        $path = $this->fs->controller_path();
        $this->assertSame($result, $path);
    }

    /** @test */
    public function should_return_correct_migration_path()
    {
        $result = path_join(database_path(), "migrations");
        $path = $this->fs->migration_path();
        $this->assertSame($result, $path);
    }

    /** @test */
    public function should_return_correct_factory_path()
    {
        $result = path_join(database_path(), "factories");
        $path = $this->fs->factory_path();
        $this->assertSame($result, $path);

    }

    /** @test */
    public function should_return_correct_default_model_path()
    {
        $result = path_join(app_path());
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
        $result = path_join(database_path(), "seeds");
        $path = $this->fs->seed_path();
        $this->assertSame($result, $path);
    }


}
