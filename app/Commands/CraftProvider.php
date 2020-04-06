<?php

namespace App\Commands;

use App\CraftsmanFileSystem;
use App\Traits\CommandDebugTrait;
use LaravelZero\Framework\Commands\Command;

/**
 * Class CraftClass
 * @package App\Commands
 */
class CraftProvider extends Command
{
    use CommandDebugTrait;

    protected $fs;

    protected $signature = 'craft:provider
                                {name : Class name}
                                {--f|facade : Create Facade}
                                {--t|template= : Template path (override configuration file)}
                                {--w|overwrite   : Overwrite existing class}
                                {--d|debug   : Use Debug Interface}
                            ';

    protected $description = "Craft Service Provider (may use any type of standard PHP class)";

    protected $help = 'Craft Class
                     <name>               Class Name
                     --facade, -f         Craft Facade Class

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
            "facade" => $this->option("facade"),
            "template" => $this->option("template"),
            "overwrite" => $this->option("overwrite"),
        ];

        $result = $this->fs->createFile('provider', $className, $data);

        return $result["status"];
    }
}
