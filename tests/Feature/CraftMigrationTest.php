<?php

namespace Tests\Feature;

use App\CraftsmanFileSystem;
use PHPUnit\Framework\Assert;
use Tests\TestCase;

class CraftMigrationTest extends TestCase
{
    protected $fs;

    function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();

        $this->fs = new CraftsmanFileSystem();
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

        $this->artisan("craft:migration {$migrationName} --model {$model} --tablename {$tablename}")
            ->assertExitCode(0);

        $this->assertMigrationFileExists($migrationName);
    }

    /** @test */
    public function should_create_migration_with_fields()
    {
        $model = "App/Models/Contacts";
        $tablename = "contacts";
        $migrationName = "create_contacts_table";
        $fieldList = "--fields fname:string@25:nullable,lname:string@50:nullable,email:string@80:nullable:unique,dob:datetime,notes:text,deleted_at:timezone";

        $this->artisan("craft:migration {$migrationName} --model {$model} --tablename {$tablename} --fields {$fieldList}")
            ->assertExitCode(0);

        $this->assertMigrationFileExists($migrationName);

        $migrationFilename = $this->fs->getLastFilename("database/migrations", $migrationName);
        $data = file_get_contents($migrationFilename);

        $this->assertStringContainsString("\$table->string('fname',25)->nullable();", $data);
        $this->assertStringContainsString("\$table->string('lname',50)->nullable();", $data);
        $this->assertStringContainsString("\$table->string('email',80)->nullable()->unique();", $data);
        $this->assertStringContainsString("\$table->datetime('dob');", $data);
        $this->assertStringContainsString("\$table->text('notes');", $data);
        $this->assertStringContainsString("\$table->timezone('deleted_at');", $data);
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
