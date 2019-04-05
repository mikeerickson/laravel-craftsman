<?php

namespace App\Commands;

use App\CraftsmanFileSystem;
use LaravelZero\Framework\Commands\Command;

class CraftTest extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'craft:test 
                                {name : Class name} 
                                {--s|setup : Include setUp block}
                                {--d|teardown : Include tearDown block}
                                {--u|unit : Create unit test}
                                {--w|overwrite : Overwrite existing test}
                            ';
    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Craft Test
                     <name>               Class Name
                     --setup, -s          Include setUp block
                     --teardown, -d       Include tearDown block
                     --unit, -u           Create unit test
                     --overwrite, -w      Overwrite existing test
            ';

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
        $setup = $this->option("setup");
        $teardown = $this->option("teardown");
        $unit = $this->option("unit");
        $overwrite = $this->option("overwrite");

        $namespace = "App\\Feature";
        if ($unit) {
            $namespace = "App\\Unit";
        }

        $data = [
            "name" => $className,
            "setup" => $setup,
            "teardown" => $teardown,
            "namespace" => $namespace,
            "overwrite" => $overwrite,
        ];

        $filename = $this->fs->path_join($unit ? "Unit" : "Feature", $className);
        $this->fs->createFile('test', $filename, $data);
    }
}
