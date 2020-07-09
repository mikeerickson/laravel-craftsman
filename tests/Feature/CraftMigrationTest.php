<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use Tests\TestHelpersTrait;
use App\CraftsmanFileSystem;
use PHPUnit\Framework\Assert;

/**
 * Class CraftMigrationTest
 * @package Tests\Feature
 */
class CraftMigrationTest extends TestCase
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

        $this->withoutExceptionHandling();

        $this->fs = new CraftsmanFileSystem();
    }

    /** @test */
    public function should_execute_default_craft_migration_command()
    {
        $class = "CreateTestsTable";
        $migrationName = "create_tests_table";

        $this->artisan("craft:migration {$migrationName}")
            ->assertExitCode(0);

        $this->assertMigrationFileExists($migrationName);

        $filename = $this->fs->getLastMigrationFilename("database/migrations", $migrationName);

        $this->assertFileContainsString($filename, "Schema::create('tests', function (Blueprint \$table) {");

        $this->cleanUp();
    }

    /**
     * @param $migrationName
     */
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

    public function cleanUp()
    {
        $this->fs->rmdir("database");
    }

    /** @test */
    public function should_create_migration_with_current_timestamp(): void
    {
        $class = "CreateTestsTable";
        $migrationName = "create_tests_table";

        $this->artisan("craft:migration {$migrationName} --current")
            ->assertExitCode(0);

        $this->assertMigrationFileExists($migrationName);

        $filename = $this->fs->getLastMigrationFilename("database/migrations", $migrationName);

        $this->assertFileContainsString($filename, $class);
        $this->assertFileContainsString($filename, "\$table->timestamp('created_at')->useCurrent();");

        $this->cleanUp();
    }

    /** @test */
    public function should_create_migration_with_foreign_constraint(): void
    {
        $migrationName = "create_tests_table";
        $foreignKey = "post_id";
        $primaryKey = "id";
        $primaryTable = "posts";

        $this->artisan("craft:migration {$migrationName} --foreign={$foreignKey}:{$primaryKey},{$primaryTable}")
            ->assertExitCode(0);

        $this->assertMigrationFileExists($migrationName);

        $filename = $this->fs->getLastMigrationFilename("database/migrations", $migrationName);

        $this->assertFileContainsString($filename, "\$table->foreign('post_id')->references('id')->on('posts');");

        $this->cleanUp();
    }

    /** @test */
    public function should_create_migration_using_foreign_key_only(): void
    {
        $migrationName = "create_tests_table";
        $foreignKey = "post_id";

        $this->artisan("craft:migration {$migrationName} --foreign {$foreignKey}")
            ->assertExitCode(0);

        $this->assertMigrationFileExists($migrationName);

        $filename = $this->fs->getLastMigrationFilename("database/migrations", $migrationName);

        $this->assertFileContainsString($filename, "\$table->foreign('{$foreignKey}')->references('id')->on('posts');");

        $this->cleanUp();
    }

    /** @test */
    public function should_execute_craft_migration_command_with_table()
    {
        $model = "App/Models/Test";
        $tablename = "tests";
        $migrationName = "create_tests_table";

        $this->artisan("craft:migration {$migrationName} --table {$tablename}")
            ->assertExitCode(0);

        $this->assertMigrationFileExists($migrationName);

        $this->cleanUp();
    }

    /** @test */
    public function should_create_migration_with_fields()
    {
        $model = "App/Models/Contacts";
        $tablename = "contacts";
        $migrationName = "create_contacts_table";
        $fieldList = "--fields fname:string@25:nullable,lname:string@50:nullable,email:string@80:nullable:unique,dob:datetime,notes:text,deleted_at:timezone";

        $this->artisan("craft:migration {$migrationName} --table {$tablename} --fields {$fieldList}")
            ->assertExitCode(0);

        $this->assertMigrationFileExists($migrationName);

        $migrationFilename = $this->fs->getLastMigrationFilename("database/migrations", $migrationName);
        $data = file_get_contents($migrationFilename);

        $this->assertStringContainsString("\$table->string('fname',25)->nullable();", $data);
        $this->assertStringContainsString("\$table->string('lname',50)->nullable();", $data);
        $this->assertStringContainsString("\$table->string('email',80)->nullable()->unique();", $data);
        $this->assertStringContainsString("\$table->datetime('dob');", $data);
        $this->assertStringContainsString("\$table->text('notes');", $data);
        $this->assertStringContainsString("\$table->timezone('deleted_at');", $data);

        $this->cleanUp();
    }

    /** @test */
    public function should_update_migration_with_fields()
    {
        $model = "App/Models/Contacts";
        $tablename = "contacts";
        $migrationName = "update_contacts_table";
        $fieldList = "--fields fname:string@25:nullable,lname:string@50:nullable,email:string@80:nullable:unique,dob:datetime,notes:text,deleted_at:timezone";

        $this->artisan("craft:migration {$migrationName} --table {$tablename} --fields {$fieldList}")
            ->assertExitCode(0);

        $this->assertMigrationFileExists($migrationName);

        $migrationFilename = $this->fs->getLastMigrationFilename("database/migrations", $migrationName);
        $data = file_get_contents($migrationFilename);

        $this->assertStringContainsString("\$table->string('fname',25)->nullable();", $data);
        $this->assertStringContainsString("\$table->string('lname',50)->nullable();", $data);
        $this->assertStringContainsString("\$table->string('email',80)->nullable()->unique();", $data);
        $this->assertStringContainsString("\$table->datetime('dob');", $data);
        $this->assertStringContainsString("\$table->text('notes');", $data);
        $this->assertStringContainsString("\$table->timezone('deleted_at');", $data);

        // $this->fs->rmdir("database/migrations");
    }

    // check to see if migration file was created. Since the filename is changed (adding timestamp)
    // we can only validate the core migration ($migrationName) is actually created

    /** @test */
    public function should_build_complex_field_data()
    {
        $migrationName = "create_contacts_table";
        $dt = Carbon::now()->format('Y_m_d_His');
        $migrationFilename = $dt . "_" . $migrationName;

        $fields = "first_name:string@20:nullable, last_name:string@60:nullable, email:string@80:nullable:unique";

        $data = [
            "model" => "App/Models/Contact",
            "tablename" => "contacts",
            "fields" => $fields,
            "create" => true,
            "update" => false,
        ];

        $this->fs->createFile("migration", $migrationFilename, $data);

        $lastFilename = $this->fs->getLastMigrationFilename("database/migrations", $migrationName);

        $this->assertFileExists($lastFilename);

        // this is not working correctly
        $this->assertFileContainsString($lastFilename, "\$table->string('first_name',20)->nullable();");

        $this->cleanUp();
    }

    /** @test */
    public function should_create_migration_without_tablename()
    {
        $migrationName = "create_product_contacts_table";

        $this->artisan("craft:migration {$migrationName}")
            ->assertExitCode(0);

        $this->assertMigrationFileExists($migrationName);

        $migrationFilename = $this->fs->getLastMigrationFilename("database/migrations", $migrationName);
        $data = file_get_contents($migrationFilename);

        $this->assertStringContainsString("CreateProductContactsTable", $data);

        $this->cleanUp();
    }

    /**
     * ===============================================================================================
     *  Migration Helpers and special asserts for migration testing only
     * ===============================================================================================
     */

    /** @test */
    public function should_create_migration_without_model()
    {
        $migrationName = "create_product_contacts_table";

        $this->artisan("craft:migration {$migrationName} --table contacts")
            ->assertExitCode(0);

        $this->assertMigrationFileExists($migrationName);

        $migrationFilename = $this->fs->getLastMigrationFilename("database/migrations", $migrationName);
        $data = file_get_contents($migrationFilename);

        $this->assertStringContainsString("CreateContactsTable", $data);

        $this->cleanUp();
    }

    /** @test */
    public function should_create_update_migration()
    {
        $migrationName = "update_contacts_table";

        $this->artisan("craft:migration {$migrationName}")
            ->assertExitCode(0);

        $this->assertMigrationFileExists($migrationName);

        $migrationFilename = $this->fs->getLastMigrationFilename("database/migrations", $migrationName);
        $data = file_get_contents($migrationFilename);

        $this->assertStringContainsString("Schema::table('contacts', function (Blueprint \$table)", $data);

        $this->cleanUp();
    }

    public function should_craft_migration_using_custom_template()
    {
        $migrationName = "create_template_migration";

        $this->artisan("craft:migration ${migrationName} --template <project>/custom-templates/migration.mustache")
            ->assertExitCode(0);

        $migrationFilename = $this->fs->getLastMigrationFilename("database/migrations", $migrationName);
        $data = file_get_contents($migrationFilename);

        $this->assertFileContainsString($migrationFilename, "// custom-migration");

        $this->cleanUp();
    }

    /** @test  */
    public function should_create_migration_using_tablename(): void
    {
        $migrationName = "create_contact_tag_table";
        $tablename = "contact_tag";

        $this->artisan("craft:migration ${migrationName} --table ${tablename}")
            ->assertExitCode(0);

        $migrationFilename = $this->fs->getLastMigrationFilename("database/migrations", $migrationName);

        $data = file_get_contents($migrationFilename);
        $this->assertFileContainsString($migrationFilename, "CreateContactTagTable");
    }
}
