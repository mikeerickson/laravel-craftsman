<?php

namespace App\Commands;

use App\CraftsmanFileSystem;
use Illuminate\Support\Facades\Artisan;
use LaravelZero\Framework\Commands\Command;

class CraftAll extends Command
{

    protected $fs;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'craft:all {name} {--m|model=} {--t|table=} {--r|rows=}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'craft:all
                     --no-controller, -c  Do not create controller
                     --no-factory, -f     Do not create factory
                     --no-migration, -g   Do not create migration
                     --no-model, -o       Do not create model
                     --no-seed, -s        Do not create seed
            ';

    public function __construct()
    {
        parent::__construct();

        $this->fs = new CraftsmanFileSystem();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->argument('name');

        $model = $this->option('model');
        $tablename = $this->option('table');
        $rows = $this->option('rows');

        Artisan::call("craft:controller {$name}Controller --model {$model}");
        $this->info("✓ {$name}Controller Created Successfully");

        Artisan::call("craft:factory {$name}Factory --model {$model}");
        $this->info("✓ {$name}Factory Created Successfully");

        Artisan::call("craft:migration create_{$tablename}_table --model {$model} --table {$tablename}");
        $this->info("✓ create_{$tablename}_table Migration Created Successfully");

        Artisan::call("craft:model {$model} --table {$tablename}");
        $this->info("✓ {$model} Model Created Successfully");

        Artisan::call("craft:seed {$name}sTableSeeder --model {$model} --rows {$rows}");
        $this->info("✓ {$name}TableSeeder Created Successfully");

        $this->warn("\nNOTES: The following tasks need to be completed manually:");
        $this->warn("       - Complete {$name} factory configuration");
        $this->warn("       - Complete {$name} migrations");
        $this->warn("       - Update 'DatabaseSeed.php' to call {$name}TableSeeder");

        $this->info("\nAsset crafting completed successfully", "Success");
    }
}
