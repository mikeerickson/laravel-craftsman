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

    /**
     * @var CraftsmanFileSystem
     */
    protected $fs;
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'craft:model 
                                {name : Model name} 
                                {--t|tablename= : Tablename if different than model name}
                                {--m|template= : Template path (override configuration file)}
                                {--w|overwrite : Overwrite existing model}
                            ';


    protected $description = "Craft Model";

    protected $help = 'Craft Model
                     <name>               Model Name (eg App\Models\Post)
                     --tablename, -t      Desired tablename
                     --template, -m       Template path (override configuration file)
                     --overwrite, -w      Overwrite existing model
            ';

    /**
     * CraftModel constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->fs = new CraftsmanFileSystem();

        $this->setHelp($this->help);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
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
            "tablename" => $tablename,
            "namespace" => $namespace,
            "overwrite" => $overwrite,
        ];

        $this->fs->createFile('model', $modelName, $data);
    }
}
