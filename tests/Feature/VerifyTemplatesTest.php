<?php

namespace Tests\Feature;

use App\CraftsmanFileSystem;
use Tests\TestCase;

class VerifyTemplatesTest extends TestCase
{
    protected $fs;

    function setUp(): void
    {
        parent::setUp();
        $this->fs = new CraftsmanFileSystem();
        $this->withoutExceptionHandling();
    }

    /** @test */
    public function should_verify_class_template_exists()
    {
        $filename = config('craftsman.templates.class');

        $this->assertFileExists($filename);
    }

    /** @test */
    public function should_verify_controller_template_exists()
    {
        $filename = config('craftsman.templates.controller');

        $this->assertFileExists($filename);
    }

    /** @test */
    public function should_verify_empty_controller_template_exists()
    {
        $filename = config('craftsman.templates.empty-controller');

        $this->assertFileExists($filename);
    }

    /** @test */
    public function should_verify_api_controller_template_exists()
    {
        $filename = config('craftsman.templates.api-controller');

        $this->assertFileExists($filename);
    }

    /** @test */
    public function should_verify_factory_template_exists()
    {
        $filename = config('craftsman.templates.factory');

        $this->assertFileExists($filename);
    }

    /** @test */
    public function should_verify_migration_template_exists()
    {
        $filename = config('craftsman.templates.migration');

        $this->assertFileExists($filename);
    }

    /** @test */
    public function should_verify_model_template_exists()
    {
        $filename = config('craftsman.templates.model');

        $this->assertFileExists($filename);
    }

    /** @test */
    public function should_verify_seed_template_exists()
    {
        $filename = config('craftsman.templates.seed');

        $this->assertFileExists($filename);
    }
}
