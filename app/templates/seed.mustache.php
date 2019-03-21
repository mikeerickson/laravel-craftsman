<?php

use App\Models\{{model}};
use Illuminate\Database\Seeder;

class {{tablename}}TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory({{model}}::class,{{num_rows}})->create();
    }
}
