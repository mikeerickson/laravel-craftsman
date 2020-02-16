<?php

namespace App\Commands;

use App\CraftsmanFileSystem;
use App\Traits\CommandDebugTrait;
use Illuminate\Support\Facades\Log;
use LaravelZero\Framework\Commands\Command;

/**
 * Class CraftClass
 * @package App\Commands
 */
class CraftEvent extends Command
{
    use CommandDebugTrait;

    protected $signature = 'craft:event
                                {name : Event name}
                                {--b|no-broadcast : Skip broadcasting}
                                {--l|listener : Generate Listener class}
                                {--t|template= : Template path (override configuration file)}
                                {--w|overwrite   : Overwrite existing class}
                                {--d|debug   : Use Debug Interface}
                            ';

    protected $description = "Craft Event Class";

    protected $help = 'Craft Event
                     <name>               Class Name
                     --no-broadcast, -b   Skip broadcasting
                     --listener, -l       Generate Listener

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
            "no-broadcast" => $this->option("no-broadcast"),
            "listener" => $this->option("listener"),
            "template" => $this->option("template"),
            "overwrite" => $this->option("overwrite"),
            // "debug" => $this->option("debug")
        ];

        $this->fs->createFile('event', $className, $data);
    }
}
