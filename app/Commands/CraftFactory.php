<?php

namespace App\Commands;

use App\CraftsmanFileSystem;
use App\Traits\CommandDebugTrait;
use LaravelZero\Framework\Commands\Command;

/**
 * Class CraftFactory
 * @package App\Commands
 */
class CraftFactory extends Command
{
    use CommandDebugTrait;

    protected $fs;

    protected $signature = 'craft:factory
                                {name : Factory Name}
                                {--m|model= : Associated model}
                                {--t|template= : Template path (override configuration file)}
                                {--w|overwrite : Overwrite existing factory}
                                {--d|debug   : Use Debug Interface}
                            ';

    protected $description = "Craft Factory";

    protected $help = 'Craft Factory
                     <name>               Factory Name
                     --model, -m          Use <model> when creating controller

                     --template, -t       Template path (override configuration file)
                     --overwrite, -w      Overwrite existing factory
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

        $factoryName = $this->argument('name');
        $model = $this->option('model');

        if (strlen($model) === 0) {
            $this->error("Must supply model name");
        } else {
            $data = [
                "model" => $model,
                "name" => $factoryName,
                "template" => $this->option('template'),
                "overwrite" => $this->option('overwrite'),
            ];

            $result = $this->fs->createFile('factory', $factoryName, $data);

            return $result["status"];

        }
    }
}
