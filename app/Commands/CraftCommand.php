<?php

namespace App\Commands;

use App\CraftsmanFileSystem;
use LaravelZero\Framework\Commands\Command;

/**
 * Class CraftClass
 * @package App\Commands
 */
class CraftCommand extends Command
{
    protected $signature = 'craft:command
                                {name : Command name}
                                {--s|signature= : Command Signature}
                                {--d|description= : Command Description}
                                {--t|template= : Template path (override configuration file)}
                                {--w|overwrite   : Overwrite existing class}
                            ';

    protected $description = "Craft Laravel Command";

    protected $help = 'Craft Command
                     <name>               Command Name
                     --signature, -s      Command Signature
                     --description, -d    Command Description

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

        $signature = $this->option('signature') ?: 'command:name';
        $description = $this->option('description') ?: 'Command description';

        $data = [
            "name" => $className,
            "signature" => $signature,
            "description" => $description,
            "template" => $this->option("template"),
            "overwrite" => $this->option("overwrite"),
        ];

        $this->fs->createFile('command', $className, $data);
    }
}
