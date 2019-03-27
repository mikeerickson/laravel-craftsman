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
        ];
        $api = $this->option('api');
        if ($api) {
            $result = $this->fs->createFile('api-controller', $controllerName, $data);
        } else {
            if (strlen($model) === 0) {
                $result = $this->fs->createFile('empty-controller', $controllerName, $data);
            } else {
                $result = $this->fs->createFile('controller', $controllerName, $data);
            }
        }

        if (getenv("APP_ENV") === "testing") {
            $this->info($result["message"]);
        } else {
            echo "\n";
            $result["status"]
                ? $this->info($result["message"])
                : $this->error($result["message"]);
        }

    }
}
