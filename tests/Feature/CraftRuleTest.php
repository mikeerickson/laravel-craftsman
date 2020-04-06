<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\TestHelpersTrait;
use App\CraftsmanFileSystem;

/**
 * Class CraftClassTest
 * @package Tests\Feature
 */
class CraftRuleTest extends TestCase
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
    public function should_craft_rule_default()
    {
        $resource = 'MyRule';

        $this->artisan("craft:rule {$resource}  --overwrite")
            ->assertExitCode(0);

        // create event
        $filename = $this->fs->path_join($this->fs->rule_path(), "{$resource}.php");

        $this->assertFileExists($filename);

        $this->assertFileContainsString($filename, "class {$resource}");

        $this->fs->delete($filename);

        $this->cleanUp();
    }

    private function cleanUp()
    {
        $this->fs->rmdir("app/Rules");
    }

    /** @test */
    public function should_craft_listener_using_custom_template()
    {
        $this->artisan("craft:rule TestRule --template <project>/custom-templates/rule.mustache --overwrite")
            ->assertExitCode(0);

        $filename = $this->fs->path_join($this->fs->rule_path(), "TestRule.php");
        $this->assertFileExists($filename);

        $this->assertFileContainsString($filename, "// custom-rule");

        $this->cleanUp();
    }
}
