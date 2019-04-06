<?php

namespace Tests\Feature;

use App\CraftsmanFileSystem;
use Tests\TestCase;
use Tests\TestHelpersTrait;

/**
 * Class CraftAllTest
 * @package Tests\Feature
 */
class CraftAllTest extends TestCase
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
    public function should_execute_craft_all_command()
    {
        $this->artisan('craft:all Author --model App/Models/Author --tablename posts --rows 44')
            ->assertExitCode(0);

        $this->fs->rmdir("app/Http");
        $this->fs->rmdir("app/Models");
        $this->fs->rmdir("app/Test");
        $this->fs->rmdir("database/migrations");
        $this->fs->rmdir("database/factories");
        $this->fs->rmdir("database/seeds");
    }
}
