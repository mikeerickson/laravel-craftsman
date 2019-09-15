<?php

namespace App\Commands;

use App\CraftsmanFileSystem;
use LaravelZero\Framework\Commands\Command;
use Codedungeon\PHPMessenger\Facades\Messenger;

/**
 * Class CraftController
 * @package App\Commands
 */
class CraftController extends Command
{
    protected $fs;

    protected $signature = 'craft:controller {name : Controller Name}
                                {--m|model= : Associated model}
                                {--r|resource : Create resource controller}
                                {--b|binding : Include route / model binding (requires model property)}
                                {--c|collection : Create resource collection}
                                {--l|validation : Scaffold validation}
                                {--a|api : Create API controller (skips create and update methods)}
                                {--t|template= : Template path (override configuration file)}
                                {--w|overwrite : Overwrite existing controller}
                           ';

    protected $description = "Craft Controller (standard, api, empty, resource)";

    protected $help = 'Craft Controller
                     <name>               Controller Name
                     --model, -m          Use <model> when creating controller
                     --validation, -l     Create validation blocks
                     --api, -a            Create API controller (skips create and update methods)
                     --empty, -e          Create empty controller
                     --resource, -r       Create resource controller
                     --binding, -b        Include Route Model Biding (requires model option)
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
        $binding = $this->option('binding');
        $api = $this->option('api');
        $resource = $this->option('resource');

        $data = [
            "model" => $model,
            "name" => $controllerName,
            "template" => $this->option('template'),
            "overwrite" => $this->option('overwrite'),
            "collection" => false,
            "binding" => $this->option('binding'),
        ];

        if ($api) {
            if (!$model) {
                Messenger::error("When creating an API controller, you must supply model", "ERROR");
                exit;
            }
            $this->fs->createFile('api-controller', $controllerName, $data);
        } elseif ($resource) {
            $data["collection"] = $this->option("collection");
            $this->fs->createFile('resource-controller', $controllerName, $data);
        } else {
            if ($binding) {
                if (strlen($data["model"]) === 0) {
                    Messenger::warning("When creating binding controllers, you must supply model", "WARNING");
                    exit;
                }

                $this->fs->createFile('binding-controller', $controllerName, $data);
            } else {
                if (strlen($model) === 0) {
                    $this->fs->createFile('empty-controller', $controllerName, $data);
                } else {
                    $this->fs->createFile('controller', $controllerName, $data);
                }
            }
        }
    }
}
