<?php

namespace App\Commands;

use App\CraftsmanFileSystem;
use App\Generators\CastGenerator;
use App\Traits\CommandDebugTrait;
use Illuminate\Support\Facades\Artisan;
use LaravelZero\Framework\Commands\Command;
use Codedungeon\PHPMessenger\Facades\Messenger;

/**
 * Class CraftCast
 * @package App\Commands
 */
class CraftCast extends Command
{
    use CommandDebugTrait;

    protected $signature = 'craft:cast
                                {name : Class name}
                                {--u|test : Create test}
                                {--w|overwrite   : Overwrite existing class}
                                {--d|debug   : Use Debug Interface}
                            ';

    protected $description = "Craft Cast";

    protected $help = 'Craft Cast
                     <name>               Class Name
                     --test, -u           Create test

                     --template, -t       Path to custom template (override config file)
                     --overwrite, -w      Overwrite existing class
            ';

    public function __construct()
    {
        parent::__construct();

        $this->fs = new CraftsmanFileSystem();
    }

    public function handle()
    {
        $this->handleDebug();

        $name = $this->argument('name');
        $namespace = $this->fs->getNamespace("cast", $name);

        $data = [
            "name" => $name,
            "className" => $this->fs->getClassName($name),
            "namespace" => $namespace,
            "overwrite" => $this->option('overwrite')
        ];

        $result = (new CastGenerator($this))->createFile();

        if ($this->option("test")) {
            $overwrite = $data["overwrite"] ? "--overwrite" : "";
            $command = "craft:test {$name}Test --unit {$overwrite}";
            Artisan::call($command);
        }

        if (!$this->option('quiet')) {
            if ($result["status"] === CraftsmanResultCodes::SUCCESS) {
                Messenger::success("{$result["message"]}\n", "SUCCESS");
            }

            if ($result["status"] === CraftsmanResultCodes::FAIL) {
                Messenger::error("{$result["message"]}\n", "ERROR");
            }
        }

        return $result["status"];
    }
}
