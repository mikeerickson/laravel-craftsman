<?php

namespace App\Commands;

use Illuminate\Support\Str;
use App\CraftsmanFileSystem;
use LaravelZero\Framework\Commands\Command;

/**
 * Class CraftTest
 * @package App\Commands
 */
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

    protected $description = "Craft Test (dusk, unit, feature)";

    protected $help = 'Craft Test
                     <name>               Class Name
                     --setup, -s          Include setUp block
                     --teardown, -d       Include tearDown block
                     --unit, -u           Create unit test
                     --overwrite, -w      Overwrite existing test
            ';

    /**
     * CraftTest constructor.
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

        if (!Str::endsWith($className, "Test")) {
            $className .= "Test";
        }

        $filename = $this->fs->path_join($unit ? "Unit" : "Feature", $className);
        $this->fs->createFile('test', $filename, $data);
    }
}
