<?php

namespace App\Commands;

use Illuminate\Support\Str;
use App\CraftsmanFileSystem;
use App\Traits\CommandDebugTrait;
use LaravelZero\Framework\Commands\Command;

/**
 * Class CraftTest
 * @package App\Commands
 */
class CraftTest extends Command
{
    use CommandDebugTrait;

    protected $fs;

    protected $signature = 'craft:test
                                {name : Class name}
                                {--s|setup : Include setUp block}
                                {--d|teardown : Include tearDown block}
                                {--u|unit : Create unit test}
                                {--p|pest : Create pest test}
                                {--t|template= : Template path (override configuration file)}
                                {--w|overwrite : Overwrite existing test}
                                {--b|debug   : Use Debug Interface}
                            ';

    protected $description = "Craft Test (dusk, unit, feature)";

    protected $help = 'Craft Test
                     <name>               Class Name
                     --setup, -s          Include setUp block
                     --teardown, -d       Include tearDown block
                     --unit, -u           Create unit test
                     --pest, -p           Create Pest test

                    --template, -t       Template path (override configuration file)
                    --overwrite, -w      Overwrite existing test
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
        if (!Str::endsWith($className, "Test")) {
            $className .= "Test";
        }

        $setup = $this->option("setup");
        $teardown = $this->option("teardown");
        $unit = $this->option("unit");
        $pest = $this->option("pest");
        $overwrite = $this->option("overwrite");

        $namespace = "App\\Feature";
        if ($unit) {
            $namespace = "App\\Unit";
        }

        $data = [
            "name" => $className,
            "pest" => $pest,
            "setup" => $setup,
            "teardown" => $teardown,
            "namespace" => $namespace,
            "overwrite" => $overwrite,
        ];

        if ($pest) {
            $filename = $this->fs->path_join($unit ? "Unit" : "Feature", $className);
        } else {
            $filename = $this->fs->path_join($unit ? "Unit" : "Feature", $className);
        }

        $result = $this->fs->createFile('test', $filename, $data);

        return $result["status"];
    }
}
