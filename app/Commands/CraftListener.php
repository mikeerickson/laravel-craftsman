<?php

namespace App\Commands;

use App\CraftsmanFileSystem;
use App\Traits\CommandDebugTrait;
use LaravelZero\Framework\Commands\Command;

/**
 * Class CraftClass
 * @package App\Commands
 */
class CraftListener extends Command
{
    use CommandDebugTrait;

    protected $fs;

    protected $signature = 'craft:listener
                                {name : Listener name}
                                {--e|event= : The event class be listener for}
                                {--queued : Indicates the event listener should be queued}
                                {--t|template= : Template path (override configuration file)}
                                {--w|overwrite   : Overwrite existing class}
                                {--d|debug   : Use Debug Interface}
                            ';

    protected $description = "Craft Listener Classes";

    protected $help = 'Craft Listener
                     <name>               Class Name
                     --event, -e          The event class be listener for
                     --queued             Indicates the event listener should be queued

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
            "event" => $this->option("event"),
            "queued" => $this->option("queued"),
            "template" => $this->option("template"),
            "overwrite" => $this->option("overwrite"),
        ];

        $data["useEvent"] = strlen($data["event"]) > 0;

        $this->fs->createFile('listener', $className, $data);
    }
}
