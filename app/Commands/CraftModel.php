<?php

namespace App\Commands;

use Illuminate\Support\Str;
use App\CraftsmanFileSystem;
use App\Traits\CommandDebugTrait;
use App\Generators\ModelGenerator;
use Illuminate\Support\Facades\Artisan;
use LaravelZero\Framework\Commands\Command;
use Codedungeon\PHPMessenger\Facades\Messenger;

/**
 * Class CraftModel
 * @package App\Commands
 */
class CraftModel extends Command
{
    use CommandDebugTrait;

    /**
     * @var CraftsmanFileSystemTest
     */
    protected $fs;

    protected $signature = 'craft:model
                                {name : Model name}
                                {--a|all : Generate a migration, factory, and resource controller for the model}
                                {--c|controller : Create a new controller for the model}
                                {--t|table= : Tablename if different than model name}
                                {--f|factory : Create a new factory for the model}
                                {--m|migration : Create a new migration file for the model}
                                {--s|seed : Create a new seeder file for the model}
                                {--l|template= : Template path (override configuration file)}
                                {--w|overwrite : Overwrite existing model}
                                {--d|debug : Debug mode}
                            ';

    protected $description = "Craft Model";

    protected $help = 'Craft Model
                     <name>               Model Name (eg App\Models\Post)
                     --all, -a            Generate a migration, factory, and resource controller for the model
                     --controller, -c     Create a new controller for the model
                     --table, -t          Desired tablename
                     --factory, -f        Create a new factory for the mode
                     --migration, -m      Create a new migration file for the model
                     --seed, -s           Create a new seed file for the model

                     --template, -l       Template path (override configuration file)
                     --overwrite, -w      Overwrite existing model
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

        $modelName = $this->argument('name');

        $overwrite = $this->option('overwrite');
        $factory = $this->option('factory');
        $seed = $this->option('seed');

        $namespace = $this->fs->getNamespace("model", $modelName);
        $model = $this->fs->getModel($modelName);

        $tablename = $this->option("table");
        $tablename = (is_null($tablename)) ? Str::plural(strtolower($model)) : $this->option("table");

        $data = [
            "model" => $model,
            "name" => $modelName,
            "className" => $model,
            "all" => $this->option('all'),
            "tablename" => $tablename,
            "factory" => $factory,
            "namespace" => $namespace,
            "overwrite" => $overwrite,
            "seed" => $seed,
        ];

        $result = (new ModelGenerator($this))->createFile();

        if (!$this->option('quiet')) {
            ($result["status"] === CraftsmanResultCodes::SUCCESS)
                ? Messenger::success("{$result["message"]}\n", "SUCCESS")
                : Messenger::error("{$result["message"]}\n", "ERROR");
        }

        if ($this->option('migration') || $this->option('all')) {
            $useCurrent = config("craftsman.miscellaneous.useCurrentDefault") ? "--current" : "";
            $command = "craft:migration create_{$tablename}_table --table {$tablename} {$useCurrent}";
            Artisan::call($command);
        }

        if ($this->option('controller') || $this->option('all')) {
            $overwrite = $data["overwrite"] ? "--overwrite" : "";
            $command = "craft:controller {$data["model"]}Controller {$overwrite}";
            Artisan::call($command);
        }

        if ($this->option('seed') || $this->option('all')) {
            $model = $data["model"];
            $overwrite = $data["overwrite"] ? "--overwrite" : "";
            $command = "craft:seed {$model}sTableSeeder --model {$data['name']} {$overwrite}";
            Artisan::call($command);
        }

        if ($this->option('factory') || $this->option('all')) {
            $model = $data["model"];
            $overwrite = $data["overwrite"] ? "--overwrite" : "";
            $command = "craft:factory {$model}Factory --model {$data['name']} {$overwrite}";
            Artisan::call($command);
        }

        return $result["status"];
    }
}
