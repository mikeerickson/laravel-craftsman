<?php

namespace App\Commands;

use App\CraftsmanFileSystem;
use App\Traits\CommandDebugTrait;
use LaravelZero\Framework\Commands\Command;

/**
 * Class CraftController
 * @package App\Commands
 */
class CraftResource extends Command
{
    use CommandDebugTrait;

    protected $fs;

    protected $signature = 'craft:resource {name : Resource Name}
                                {--c|collection : Create resource collection}
                                {--t|template= : Template path (override configuration file)}
                                {--w|overwrite : Overwrite existing controller}
                                {--d|debug   : Use Debug Interface}
                           ';

    protected $description = "Craft Resource";

    protected $help = 'Craft Resource
                     <name>               Controller Name
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
        $this->handleDebug();

        $controllerName = $this->argument('name');

        $data = [
            "name" => $controllerName,
            "template" => $this->option('template'),
            "overwrite" => $this->option('overwrite'),
            "collection" => $this->option('collection'),
        ];

        $result = $this->fs->createFile('resource', $controllerName, $data);

        return $result["status"];
    }
}
