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
                                {--w|overwrite : Overwrite existing class}
                            ';
    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Craft Class
                     <name>               Class Name
                     --constructor, -c    Include constructor method
                     --overwrite, -w      Overwrite existing class
            ';

    /**
     * CraftClass constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->fs = new CraftsmanFileSystem();
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
            "overwrite" => $this->option("overwrite"),
        ];

        $this->fs->createFile('class', $className, $data);
    }
}
