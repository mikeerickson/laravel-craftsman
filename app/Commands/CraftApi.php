<?php

namespace App\Commands;

use Illuminate\Support\Str;
use App\CraftsmanFileSystem;
use App\Traits\CommandDebugTrait;
use Illuminate\Support\Facades\Artisan;
use LaravelZero\Framework\Commands\Command;
use Codedungeon\PHPMessenger\Facades\Messenger;

class CraftApi extends Command
{
    use CommandDebugTrait;

    protected $fs;

    protected $signature = 'craft:api
                                {name : Base Entity used by rest of commands}
                                {--m|model= : Path to model (default App path)}
                                {--t|tablename= : Desired tablename (default to pluarlized resource name)}
                                {--u|current : Use --useCurrent for timestamps (migration)}
                                {--r|rows= : Number of rows created by migration command}

                                {--c|no-controller : Skip crafting controller}
                                {--f|no-factory : Skip crafting factory}
                                {--g|no-migration : Skip crafting migration}
                                {--o|no-model : Skip crafting model (supplied model still be used in other resources)}
                                {--s|no-seed : Skip crafting seed}

                                {--w|overwrite   : Overwrite existing class}
                            ';

    protected $description = 'Craft API Resources (create model, controller, factory, migration)';

    protected $help = 'Craft API
                     <name>               Resource Name (ie Contact, Post, Comment)
                     --model, -m          Path to model [default: app/]
                     --table, -t          Desired tablename [default to pluarlized resource name]
                     --rows, -r           Number of rows for migration [default: 1] (passed to seeder)
                     --current, -u        Use --useCurrent for timestamps when creating migration
                     --no-model, -o       Skip crafting model
                     --no-controller, -c  Skip crafting controller
                     --no-factory, -f     Skip crafting factory
                     --no-migration, -g   Skip crafting migration
                     --no-seed, -s        Skip crafting seed

                     --overwrite, -w      Overwrite existing class
            ';

    public function __construct()
    {
        parent::__construct();

        $this->setHelp($this->help);

        $this->fs = new CraftsmanFileSystem();
    }

    public function handle()
    {
        $this->handleDebug();

        // required options
        $name = $this->argument('name');
        $model = $this->option('model');
        if (!$model) {
            Messenger::error("When creating an API resources, you must supply model", "ERROR");
            exit;
        }

        $tablename = $this->option('tablename');
        $noModel = $this->option('no-model');
        $noController = $this->option('no-controller');
        $noFactory = $this->option('no-factory');
        $noMigration = $this->option('no-migration');
        $noSeed = $this->option('no-seed');
        $rows = $this->option('rows') ?: '1';

        // boolean options
        $overwrite = $this->option('overwrite') ? '--overwrite' : '';
        $useCurrent = $this->option('current') ? '--current' : '';

        if (strlen($tablename) === 0) {
            $tablename = Str::plural(strtolower($name));
        }

        $this->info("\n");

        $controllerName = "API/Api{$name}Controller";

        if (!$noController) {
            Artisan::call("craft:controller $controllerName --api --model {$model} {$overwrite}");
        } else {
            Messenger::info("▶︎ Controller crafting skipped\n");
        }

        if (!$noFactory) {
            Artisan::call("craft:factory {$name}Factory --model {$model} {$overwrite}");
        } else {
            Messenger::info("▶︎ Factory crafting skipped\n");
        }

        if (!$noMigration) {
            Artisan::call("craft:migration create_{$tablename}_table --model {$model} --table {$tablename} {$useCurrent}");
        } else {
            Messenger::info("▶︎ Migration crafting skipped\n");
        }

        if (!$noModel) {
            Artisan::call("craft:model {$model} --table {$tablename} {$overwrite}");
        } else {
            Messenger::info("▶︎ Model crafting skipped\n");
        }

        if (!$noSeed) {
            Artisan::call("craft:seed {$name}sTableSeeder --model {$model} --rows {$rows} {$overwrite}");
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
            Messenger::warning("       ⚈  Run 'composer dump-autoload' after you have completed above steps");
        }

        Messenger::info("\n================================================================================\n");

        if ($skipAll) {
            Messenger::warning("You skipped all assets, nothing created", "WARNING");
        } else {
            Messenger::info("Asset Crafting Complete", "COMPLETE");
        }
    }
}
