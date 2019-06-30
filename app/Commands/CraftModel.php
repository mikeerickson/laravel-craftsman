<?php

namespace App\Commands;

use Illuminate\Support\Str;
use App\CraftsmanFileSystem;
use LaravelZero\Framework\Commands\Command;

/**
 * Class CraftModel
 * @package App\Commands
 */
class CraftModel extends Command
{
    protected $fs;

    protected $signature = 'craft:model
                                {name : Model name}
                                {--a|all : Generate a migration, factory, and resource controller for the model}
                                {--c|collection : Use collections (only used when --all supplied)}
                                {--t|tablename= : Tablename if different than model name}
                                {--m|template= : Template path (override configuration file)}
                                {--w|overwrite : Overwrite existing model}
                            ';

    protected $description = "Craft Model";

    protected $help = 'Craft Model
                     <name>               Model Name (eg App\Models\Post)
                     --all, -a            Generate a migration, factory, and resource controller for the model
                     --collection, -c     Use collections (only used when --all supplied)
                     --tablename, -t      Desired tablename

                     --template, -m       Template path (override configuration file)
                     --overwrite, -w      Overwrite existing model
            ';

    public function __construct()
    {
        parent::__construct();

        $this->fs = new CraftsmanFileSystem();

        $this->setHelp($this->help);
    }

    public function handle()
    {
        $modelName = $this->argument('name');
        $overwrite = $this->option('overwrite');

        $parts = explode("/", $modelName);
        $model = array_pop($parts);
        $namespace = count($parts) > 0 ? implode($parts, "\\") : "App";

        $tablename = $this->option("tablename");
        if (strlen($tablename) === 0) {
            $tablename = Str::plural(strtolower($model));
        }
        $data = [
            "model" => $model,
            "name" => $modelName,
            "all" => $this->option('all'),
            "tablename" => $tablename,
            "namespace" => $namespace,
            "overwrite" => $overwrite,
            "collection" => $this->option('collection'),
        ];

        $this->fs->createFile('model', $modelName, $data);
    }
}
