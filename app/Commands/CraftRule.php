<?php

namespace App\Commands;

use App\CraftsmanFileSystem;
use App\Traits\CommandDebugTrait;
use LaravelZero\Framework\Commands\Command;

/**
 * Class CraftClass
 * @package App\Commands
 */
class CraftRule extends Command
{
    use CommandDebugTrait;

    protected $fs;

    protected $signature = 'craft:rule
                                {name : The name of `rule` class}
                                {--t|template= : Template path (override configuration file)}
                                {--w|overwrite   : Overwrite existing class}
                                {--d|debug   : Use Debug Interface}
                            ';

    protected $description = "Craft Validation Rule";

    protected $help = 'Craft Validation Rule
                     <name>               Rule Name
                     --template, -t       Path to custom template (override config file)
                     --overwrite, -w      Overwrite existing class
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

        $className = $this->argument('name');

        $data = [
            "name" => $className,
            "template" => $this->option("template"),
            "overwrite" => $this->option("overwrite"),
        ];

        $result = $this->fs->createFile('rule', $className, $data);

        return $result["status"];
    }
}
