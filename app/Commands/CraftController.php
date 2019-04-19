<?php

namespace App\Commands;

use App\CraftsmanFileSystem;
use LaravelZero\Framework\Commands\Command;

/**
 * Class CraftController
 * @package App\Commands
 */
class CraftController extends Command
{
    protected $fs;

    protected $signature = 'craft:controller {name : Controller Name} 
                                {--m|model= : Associated model} 
                                {--w|overwrite : Overwrite existing controller} 
                                {--u|resource : Create resource controller} 
                                {--c|collection : Create resource collection} 
                                {--t|template= : Template path (override configuration file)} 
                                {--l|validation : Scaffold validation} 
                                {--a|api : Create API controller (skips create and update methods)}
                           ';

    protected $description = "Craft Controller (standard, api, empty, resource)";

    protected $help = 'Craft Controller
                     <name>               Controller Name
                     --model, -m          Use <model> when creating controller
                     --validation, -l     Create validation blocks
                     --api, -a            Create API controller (skips create and update methods)
                     --empty, -e          Create empty controller
                     --resource, -u       Create resource controller
                     --collection, -c     Use resource collection
                     --template, -t       Template path (override configuration file)
                     --overwrite, -w      Overwrite existing controller
            ';

    public function __construct()
    {
        parent::__construct();

        $this->fs = new CraftsmanFileSystem();

        $this->setHelp($this->help);
    }

    public function handle()
    {
        $controllerName = $this->argument('name');
        $model = $this->option('model');

        $data = [
            "model" => $model,
            "name" => $controllerName,
            "template" => $this->option('template'),
            "overwrite" => $this->option('overwrite'),
            "collection" => false,
        ];

        $api = $this->option('api');
        $resource = $this->option('resource');

        if ($api) {
            $this->fs->createFile('api-controller', $controllerName, $data);
        } elseif ($resource) {
            $data["collection"] = $this->option("collection");
            $this->fs->createFile('resource-controller', $controllerName, $data);
        } else {
            if (strlen($model) === 0) {
                $this->fs->createFile('empty-controller', $controllerName, $data);
            } else {
                $this->fs->createFile('controller', $controllerName, $data);
            }
        }
    }
}
