<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\TestHelpersTrait;
use App\CraftsmanFileSystem;

/**
 * Class CraftClassTest
 * @package Tests\Feature
 */
class CraftEventTest extends TestCase
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
    public function should_craft_event_default()
    {
        $resource = 'MyEvent';

        $this->artisan("craft:event {$resource}  --overwrite")
            ->assertExitCode(0);

        // create event
        $filename = $this->fs->path_join($this->fs->event_path(), "{$resource}.php");

        $this->assertFileExists($filename);

        $this->assertFileContainsString($filename, "broadcast");

        $this->assertFileContainsString($filename, "class {$resource}");

        $this->fs->delete($filename);

        $this->cleanUp();
    }

    private function cleanUp()
    {
        $this->fs->rmdir("app/Events");
    }

    /** @test */
    public function should_create_event_using_custom_path(): void
    {
        $resource = "Products/ProductEvent";

        $this->artisan("craft:event {$resource} --overwrite")
            ->assertExitCode(0);

        // create model
        $filename = $this->fs->path_join($this->fs->event_path(), "Products", "ProductEvent.php");
        $this->assertFileExists($filename);
        unlink($filename);

        $this->cleanUp();
    }

    /** @test */
    public function should_create_event_skipping_broadcast()
    {
        $resource = "Product";

        $this->artisan("craft:event {$resource} --no-broadcast --overwrite")
            ->assertExitCode(0);

        $filename = $this->fs->path_join($this->fs->event_path(), "{$resource}.php");

        $this->assertFileNotContainString($filename, "broadcast");

        unlink($filename);

        $this->cleanUp();
    }

    /** @test */
    public function should_create_event_listener(): void
    {

        $resource = "ProductEvent";

        $this->artisan("craft:event {$resource} --no-broadcast --listener --overwrite")
            ->assertExitCode(0);

        $listenerFilename = $this->fs->path_join($this->fs->listener_path(), "{$resource}Listener.php");

        $this->assertFileExists($listenerFilename);

        $this->assertFileContainsString($listenerFilename, "class ${resource}Listener");

        unlink($listenerFilename);

        $this->cleanUp();
    }
}
