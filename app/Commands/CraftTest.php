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
                            ';
    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Craft Class
                     <name>               Class Name
                     --setup, -s          Include setUp block
                     --teardown, -d       Include tearDown block
                     --unit, -u           Create unit test
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

        $namespace = "App\\Feature";
        if ($unit) {
            $namespace = "App\\Unit";
        }

        $data = [
            "name" => $className,
            "setup" => $setup,
            "teardown" => $teardown,
            "namespace" => $namespace,
        ];

        $filename = $this->fs->path_join($unit ? "Unit" : "Feature", $className);
        $result = $this->fs->createFile('test', $filename, $data);

        if (getenv("APP_ENV") === "testing") {
            $this->info($result["message"]);
        } else {
            echo "\n";
            $this->info("✔︎ ".$result["message"]);
        }
    }
}
