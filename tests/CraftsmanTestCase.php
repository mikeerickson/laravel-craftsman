<?php

namespace Tests;

use Illuminate\Filesystem\Filesystem;
use LaravelZero\Framework\Testing\TestCase as BaseTestCase;

abstract class CraftsmanTestCase extends BaseTestCase
{
    use CreatesApplication;

    protected $fs;

    function setUp(): void
    {
        parent::setUp();

        $this->fs = new Filesystem();

        // remove all output directories so we start fresh for each test run
        $this->fs->exists("app/Http") ? $this->fs->deleteDirectory("app/Http") : null;
        $this->fs->exists("database/migrations") ? $this->fs->deleteDirectory("database/migrations") : null;
        $this->fs->exists("database/factories") ? $this->fs->deleteDirectory("database/factories") : null;
        $this->fs->exists("databse/seeds") ? $this->fs->deleteDirectory("database/seeds") : null;
    }

}
