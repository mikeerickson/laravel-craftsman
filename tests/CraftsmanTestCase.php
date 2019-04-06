<?php

namespace Tests;

use App\CraftsmanFileSystem;
use LaravelZero\Framework\Testing\TestCase as BaseTestCase;

/**
 * Class CraftsmanTestCase
 * @package Tests
 */
abstract class CraftsmanTestCase extends BaseTestCase
{
    use CreatesApplication, TestHelpersTrait;
    
    /**
     * @var
     */
    protected $fs;

    /**
     *
     */
    function setUp(): void
    {
        parent::setUp();

        $this->fs = new CraftsmanFileSystem();
    }

    /**
     * setUpBeforeClass
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
    }

    /**
     * tearDownAfterClass
     */
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        rmdir("resources/views/coverage");
    }


}
