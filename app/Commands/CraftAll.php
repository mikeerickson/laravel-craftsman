<?php

namespace App\Commands;

use App\CraftsmanFileSystem;
use Codedungeon\PHPMessenger\Facades\Messenger;
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
    protected $signature = 'craft:all 
                                {name : Base Entity used by rest of commands} 
                                {--m|model= : Associated model} 
                                {--t|table= : Associated tablename} 
                                {--r|rows= : Number of rows created by migration command} 
                                
                                {--c|no-controller : Skip crafting controller}
                                {--f|no-factory : Skip crafting factory}
                                {--g|no-migration : Skip crafting migration}
                                {--o|no-model : Skip crafting model}
                                {--s|no-seed : Skip crafting seed}
                            ';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Craft All Assets
                     <name>               Base Asset Name
                     --model, -m          Model Name
                     --table, -t          Tablename
                     --rows, -r           Number of rows for migration (passed to factory
                     
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

        // grab any options to skip assets
        $noController = $this->option('no-controller');
        $noFactory = $this->option('no-factory');
        $noMigration = $this->option('no-migration');
        $noModel = $this->option('no-model');
        $noSeed = $this->option('no-seed');

        $this->info("\n");

        if (!$noController) {
            Artisan::call("craft:controller {$name}Controller --model {$model}");
            $this->info("✔︎ app/Http/Controllers/{$name}Controller Created Successfully");
        } else {
            Messenger::info("Controller crafting skipped", "INFO");
        }

        if (!$noFactory) {
            Artisan::call("craft:factory {$name}Factory --model {$model}");
            $this->info("✔︎ database/factories/{$name}Factory Created Successfully");
        } else {
            Messenger::info("Factory crafting skipped", "INFO");
        }

        if (!$noMigration) {
            Artisan::call("craft:migration create_{$tablename}_table --model {$model} --table {$tablename}");
            $this->info("✔︎ database/migrations/create_{$tablename}_table Created Successfully");
        } else {
            Messenger::info("Migration crafting skipped", "INFO");
        }

        if (!$noModel) {
            Artisan::call("craft:model {$model} --tablename {$tablename}");
            $this->info("✔︎ {$model} Created Successfully");
        } else {
            Messenger::info("Model crafting skipped", "INFO");
        }

        if (!$noSeed) {
            Artisan::call("craft:seed {$name}sTableSeeder --model {$model} --rows {$rows}");
            $this->info("✔︎ database/seeds/{$name}TableSeeder Created Successfully");
        } else {
            Messenger::info("Seed crafting skipped", "INFO");
        }

        $skipAll = false;
        if ($noController && $noFactory && $noMigration && $noModel && $noSeed) {
            $skipAll = true;
        } else {
            $this->warn("\nNOTES: The following tasks need to be completed manually:\n");
        }

        if (!$noFactory) {
            $this->warn("       ⚈  Complete {$name} factory configuration");
        }

        if (!$noMigration) {
            $this->warn("       ⚈  Complete {$name} migrations");
        }

        if (!$noSeed) {
            $this->warn("       ⚈  Update 'database/seeds/DatabaseSeed.php' to call {$name}sTableSeeder");
        }

        Messenger::success("\n================================================================================\n");

        if ($skipAll) {
            Messenger::warning("You skipped all assets, nothing created", "WARNING");
        } else {
            Messenger::success("Asset Crafting Completed Successfully", " SUCCESS ");
        }
    }
}
