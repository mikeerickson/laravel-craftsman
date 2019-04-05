<?php

namespace App\Commands;

use App\CraftsmanFileSystem;
use LaravelZero\Framework\Commands\Command;

class CraftController extends Command
{

    protected $fs;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'craft:controller {name : Controller Name} 
                                {--m|model= : Associated model} 
                                {--w|overwrite : Overwrite existing controller} 
                                {--l|validation : Scaffold validation} 
                                {--a|api : create API controller (skips create and update methods)}
                           ';
    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Craft Controller
                     <name>               Controller Name
                     --model, -m          Use <model> when creating controller
                     --validation, -l     Create validation blocks
                     --api, -a            Create API controller (skips create and update methods)
                     --empty, -e          Create empty controller
                     --overwrite, -w      Overwrite existing controller
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
        $controllerName = $this->argument('name');
        $model = $this->option('model');

        $data = [
            "model" => $model,
            "name" => $controllerName,
            "overwrite" => $this->option('overwrite'),
        ];

        $api = $this->option('api');
        if ($api) {
            $this->fs->createFile('api-controller', $controllerName, $data);
        } else {
            if (strlen($model) === 0) {
                $this->fs->createFile('empty-controller', $controllerName, $data);
            } else {
                $this->fs->createFile('controller', $controllerName, $data);
            }
        }
    }
}
