<?php

namespace App\Commands;

use App\CraftsmanFileSystem;
use App\Generators\JobGenerator;
use App\Traits\CommandDebugTrait;
use Illuminate\Support\Facades\Artisan;
use LaravelZero\Framework\Commands\Command;
use Codedungeon\PHPMessenger\Facades\Messenger;

/**
 * Class CraftJob
 * @package App\Commands
 */
class CraftJob extends Command
{
    use CommandDebugTrait;

    protected $signature = 'craft:job
                                {name : Class name}
                                {--s|sync : Create synchronous job}
                                {--u|test : Create test}
                                {--w|overwrite   : Overwrite existing class}
                                {--d|debug   : Use Debug Interface}
                            ';

    protected $description = "Craft Job";

    protected $help = 'Craft Job
                     <name>               Class Name
                     --sync, -s           Create synchronous job
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
        $namespace = $this->fs->getNamespace("job", $name);

        $data = [
            "name" => $name,
            "className" => $this->fs->getClassName($name),
            "sync" => $this->option('sync'),
            "namespace" => $namespace,
            "overwrite" => $this->option('overwrite')
        ];

        $result = (new JobGenerator($this))->createFile();

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
