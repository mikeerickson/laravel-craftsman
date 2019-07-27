<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\CraftsmanFileSystem;

/**
 * Class VerifyTemplatesTest
 * @package Tests\Feature
 */
class VerifyTemplatesTest extends TestCase
{
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
    public function should_verify_class_template_exists()
    {
        $filename = $this->fs->getTemplateFilename("class");

        $this->assertFileExists($filename);
    }

    /** @test */
    public function should_verify_controller_template_exists()
    {
        $filename = $this->fs->getTemplateFilename("controller");

        $this->assertFileExists($filename);
    }

    /** @test */
    public function should_verify_empty_controller_template_exists()
    {
        $filename = $this->fs->getTemplateFilename("empty-controller");

        $this->assertFileExists($filename);
    }

    /** @test */
    public function should_verify_api_controller_template_exists()
    {
        $filename = $this->fs->getTemplateFilename("api-controller");

        $this->assertFileExists($filename);
    }

    /** @test */
    public function should_verify_factory_template_exists()
    {
        $filename = $this->fs->getTemplateFilename("factory");

        $this->assertFileExists($filename);
    }

    /** @test */
    public function should_verify_migration_template_exists()
    {
        $filename = $this->fs->getTemplateFilename("migration");

        $this->assertFileExists($filename);
    }

    /** @test */
    public function should_verify_model_template_exists()
    {
        $filename = $this->fs->getTemplateFilename("model");

        $this->assertFileExists($filename);
    }

    /** @test */
    public function should_verify_seed_template_exists()
    {
        $filename = $this->fs->getTemplateFilename("seed");

        $this->assertFileExists($filename);
    }

    /** @test */
    public function should_verify_test_template_exists()
    {
        $filename = $this->fs->getTemplateFilename("test");

        $this->assertFileExists($filename);
    }

    /** @test */
    public function should_verify_view_create_template_exists()
    {
        $filename = $this->fs->getTemplateFilename("view-create");

        $this->assertFileExists($filename);
    }

    /** @test */
    public function should_verify_view_edit_template_exists()
    {
        $filename = $this->fs->getTemplateFilename("view-edit");

        $this->assertFileExists($filename);
    }

    /** @test */
    public function should_verify_view_index_template_exists()
    {
        $filename = $this->fs->getTemplateFilename("view-index");

        $this->assertFileExists($filename);
    }

    /** @test */
    public function should_verify_view_show_template_exists()
    {
        $filename = $this->fs->getTemplateFilename("view-show");

        $this->assertFileExists($filename);
    }

    /** @test */
    public function should_use_custom_template_overriding_default()
    {
        $filename = $this->fs->getTemplateFilename("<project>/templates/custom.mustache");
        $this->assertFileExists($filename);
    }

    /** @test */
    public function should_create_asset_using_custom_templates()
    {
        $className = "App/Services/TestService";
        $mergeData = [
            "name" => $className,
            "template" => "<project>/templates/custom.mustache",
            "overwrite" => true,
        ];
        $assetFilename = $this->fs->createFile("class", $className, $mergeData);

        $this->assertFileExists($assetFilename["fullPath"]);
        $this->assertFileExists($assetFilename["filename"]);

        $this->fs->rmdir("App/Services");
    }
}
