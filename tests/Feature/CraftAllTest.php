<?php

namespace Tests\Feature;

use Tests\CraftsmanTestCase;

// TODO: Add code to delete resource directory instead of just filenames

class CraftAllTest extends CraftsmanTestCase
{
    function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();
    }

    /** @test */
    public function should_execute_craft_all_command()
    {
        $this->artisan('craft:all Author --model App/Models/Author --tablename posts --rows 44')
            ->assertExitCode(0);
    }
}
