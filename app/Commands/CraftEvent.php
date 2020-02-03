<?php

namespace App\Commands;

use App\CraftsmanFileSystem;
use LaravelZero\Framework\Commands\Command;

/**
 * Class CraftClass
 * @package App\Commands
 */
class CraftEvent extends Command
{
    protected $signature = 'craft:event
                                {name : Event name}
                                {--b|no-broadcast : Skip broadcasting}
                                {--t|template= : Template path (override configuration file)}
                                {--w|overwrite   : Overwrite existing class}
                            ';

    protected $description = "Craft Event Class";

    protected $help = 'Craft Event
                     <name>               Class Name
                     --no-broadcast, -b   Skip broadcasting

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
        $className = $this->argument('name');

        $data = [
            "name" => $className,
            "no-broadcast" => $this->option("no-broadcast"),
            "template" => $this->option("template"),
            "overwrite" => $this->option("overwrite"),
        ];

        $this->fs->createFile('event', $className, $data);
    }
}
