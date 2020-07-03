<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\TestHelpersTrait;
use App\CraftsmanFileSystem;

/**
 * Class CraftClassTest
 * @package Tests\Feature
 */
class CraftListenerTest extends TestCase
{
    use TestHelpersTrait;

    protected $fs;

    public function setUp(): void
    {
        parent::setUp();

        $this->fs = new CraftsmanFileSystem();

        $this->withoutExceptionHandling();
    }

    /** @test */
    public function should_craft_listener_default()
    {
        $resource = 'MyListener';

        $this->artisan("craft:listener {$resource}  --overwrite")
            ->assertExitCode(0);

        // create event
        $filename = $this->fs->path_join($this->fs->listener_path(), "{$resource}.php");

        $this->assertFileExists($filename);

        $this->assertFileContainsString($filename, "class {$resource}");

        $this->fs->delete($filename);

        $this->cleanUp();
    }

    private function cleanUp()
    {
        $this->fs->rmdir("app/Listeners");
    }

    /** @test */
    public function should_create_listener_with_event()
    {
        $resource = "MyListener";

        $this->artisan("craft:listener {$resource} --event MyEvent --overwrite")
            ->assertExitCode(0);

        $filename = $this->fs->path_join($this->fs->listener_path(), "{$resource}.php");

        $this->assertFileContainsString($filename, "use App\Events\MyEvent");
        $this->assertFileContainsString($filename, "public function handle(MyEvent");

        unlink($filename);

        $this->cleanUp();
    }

    /** @test */
    public function should_craft_listener_using_custom_template()
    {
        $this->artisan("craft:listener TestListener --template <project>/custom-templates/listener.mustache --overwrite")
            ->assertExitCode(0);

        $filename = $this->fs->path_join($this->fs->listener_path(), "TestListener.php");
        $this->assertFileExists($filename);

        $this->assertFileContainsString($filename, "// custom-listener");

        $this->cleanUp();
    }
}
