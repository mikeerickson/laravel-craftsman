<?php

namespace App\Commands;

use App\CraftsmanFileSystem;
use App\Traits\CommandDebugTrait;
use App\Generators\ClassGenerator;
use LaravelZero\Framework\Commands\Command;
use Codedungeon\PHPMessenger\Facades\Messenger;

/**
 * Class CraftClass
 * @package App\Commands
 */
class CraftClass extends Command
{
    use CommandDebugTrait;

    protected $signature = 'craft:class
                                {name : Class name}
                                {--c|constructor : Include constructor method}
                                {--t|template= : Template path (override configuration file)}
                                {--w|overwrite   : Overwrite existing class}
                                {--d|debug   : Use Debug Interface}
                            ';

    protected $description = "Craft Standard Class (may use any type of standard PHP class)";

    protected $help = 'Craft Class
                     <name>               Class Name
                     --constructor, -c    Include constructor method

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
        $this->handleDebug();

        $result = (new ClassGenerator($this))->createFile();

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
