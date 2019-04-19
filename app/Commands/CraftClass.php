<?php

namespace App\Commands;

use App\CraftsmanFileSystem;
use LaravelZero\Framework\Commands\Command;

/**
 * Class CraftClass
 * @package App\Commands
 */
class CraftClass extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'craft:class 
                                {name : Class name} 
                                {--c|constructor : Include constructor method}
                                {--t|template= : Template path (override configuration file)}
                                {--w|overwrite   : Overwrite existing class}
                            ';

    protected $description = "Craft Standard Classes (may use any type of standard PHP class)";

    protected $help = 'Craft Class
                     <name>               Class Name
                     --constructor, -c    Include constructor method
                     --template, -t       Path to custom template (override config file)
                     --overwrite, -w      Overwrite existing class
            ';

    /**
     * CraftClass constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->fs = new CraftsmanFileSystem();

        $this->setHelp($this->help);

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $className = $this->argument('name');

        $data = [
            "name" => $className,
            "constructor" => $this->option("constructor"),
            "template" => $this->option("template"),
            "overwrite" => $this->option("overwrite"),
        ];

        $this->fs->createFile('class', $className, $data);
    }
}
