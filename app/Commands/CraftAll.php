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
                                {--t|tablename= : Associated tablename} 
                                {--f|fields= : List of fields used in migration} 
                                {--r|rows= : Number of rows created by migration command} 
                                
                                {--c|no-controller : Skip crafting controller}
                                {--a|no-factory : Skip crafting factory}
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
                     --tablename, -t      Tablename
                     --fields, -f         Field List (passed to migration)
                                           eg. --fields first_name:string,20^nullable^unique, last_name:string,20
                     --rows, -r           Number of rows for migration (passed to factory)
                     
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
        $tablename = $this->option('tablename');
        $rows = $this->option('rows');
        $fields = $this->option('fields');

        // grab any options to skip assets
        $noController = $this->option('no-controller');
        $noFactory = $this->option('no-factory');
        $noMigration = $this->option('no-migration');
        $noModel = $this->option('no-model');
        $noSeed = $this->option('no-seed');


        $this->info("\n");

        if (!$noController) {
            Artisan::call("craft:controller {$name}Controller --model {$model}");
            Messenger::success("✔︎ app/Http/Controllers/{$name}Controller Created Successfully");
        } else {
            Messenger::info("▶︎ Controller crafting skipped\n");
        }

        if (!$noFactory) {
            Artisan::call("craft:factory {$name}Factory --model {$model}");
            Messenger::success("✔︎ database/factories/{$name}Factory Created Successfully");
        } else {
            Messenger::info("▶︎ Factory crafting skipped\n");
        }

        if (!$noMigration) {
            Artisan::call("craft:migration create_{$tablename}_table --model {$model} --tablename {$tablename} --fields {$fields}");
            Messenger::success("✔︎ database/migrations/create_{$tablename}_table Migration Created Successfully");
        } else {
            Messenger::info("▶︎ Migration crafting skipped\n");
        }

        if (!$noModel) {
            Artisan::call("craft:model {$model} --tablename {$tablename}");
            Messenger::success("✔︎ {$model} Model Created Successfully");
        } else {
            Messenger::info("▶︎ Model crafting skipped\n");
        }

        if (!$noSeed) {
            Artisan::call("craft:seed {$name}sTableSeeder --model {$model} --rows {$rows}");
            Messenger::success("✔︎ database/seeds/{$name}TableSeeder Created Successfully");
        } else {
            Messenger::info("▶︎ Seed crafting skipped\n");
        }

        $skipAll = false;
        if ($noController && $noFactory && $noMigration && $noModel && $noSeed) {
            $skipAll = true;
        } else {
            Messenger::warning("\nNOTES: The following tasks need to be completed manually:\n");
        }

        if (!$noFactory) {
            Messenger::warning("       ⚈  Complete {$name} factory configuration");
        }

        if (!$noMigration) {
            Messenger::warning("       ⚈  Complete {$name} migrations");
        }

        if (!$noSeed) {
            Messenger::warning("       ⚈  Update 'database/seeds/DatabaseSeed.php' to call {$name}sTableSeeder");
        }

        Messenger::success("\n================================================================================\n");

        if ($skipAll) {
            Messenger::warning("You skipped all assets, nothing created", "WARNING");
        } else {
            Messenger::success("Asset Crafting Completed Successfully", "SUCCESS");
        }
    }
}
