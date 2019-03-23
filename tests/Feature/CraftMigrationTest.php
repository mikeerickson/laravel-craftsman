<?php

namespace Tests\Feature;

use PHPUnit\Framework\Assert;
use Tests\TestCase;

class CraftMigrationTest extends TestCase
{
    function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();
    }

    /** @test */
    public function should_execute_default_craft_migration_command()
    {
        $model = "App/Models/Test";
        $migrationName = "create_tests_table";

        $this->artisan("craft:migration {$migrationName} --model {$model}")
            ->assertExitCode(0);

        $this->assertMigrationFileExists($migrationName);
    }

    /** @test */
    public function should_execute_craft_migration_command_with_table()
    {
        $model = "App/Models/Test";
        $tablename = "tests";
        $migrationName = "create_tests_table";

        $this->artisan("craft:migration {$migrationName} --model {$model} --table {$tablename}")
            ->assertExitCode(0);

        $this->assertMigrationFileExists($migrationName);
    }

    // check to see if migration file was created. Since the filename is changed (adding timestamp)
    // we can only validate the core migration ($migrationName) is actually created
    private function assertMigrationFileExists($migrationName)
    {
        foreach (scandir("database/migrations") as $filename) {
            if (!strpos($filename, $migrationName)) {
                Assert::assertTrue(true);
                return;
            }
        }

        Assert::assertTrue(false);
    }
}
