<?php

namespace Tests\Unit;

use Tests\TestCase;

/**
 * Class HelpersTest
 * @package Tests\Unit
 */
class HelpersTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */

    /** @test */
    public function should_return_correct_controller_path()
    {
        $result = path_join(app_path(), "Http", "Controllers");
        $this->assertStringContainsStringIgnoringCase('/laravel-craftsman/app/Http/Controllers', $result);
    }

    /** @test */
    public function should_return_valid_path()
    {
        $migration_path = path_join(database_path(), "migrations");
        $this->assertTrue(valid_path($migration_path));
    }
}
