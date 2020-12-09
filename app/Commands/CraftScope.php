<?php

namespace App\Commands;

use App\CraftsmanFileSystem;
use App\Traits\CommandDebugTrait;
use App\Generators\ScopeGenerator;
use Illuminate\Support\Facades\Artisan;
use LaravelZero\Framework\Commands\Command;
use Codedungeon\PHPMessenger\Facades\Messenger;

/**
 * Class CraftScope
 * @package App\Commands
 */
class CraftScope extends Command
{
    use CommandDebugTrait;

    /**
     * @var CraftsmanFileSystemTest
     */
    protected $fs;

    protected $signature = 'craft:scope
                                {name : Scope name}
                                {--l|template= : Template path (override configuration file)}
                                {--w|overwrite : Overwrite existing scope}
                                {--d|debug : Debug mode}
                            ';

    protected $description = "Craft Scope";

    protected $help = 'Craft Scope
                     <name>               Scope Name

                     --template, -l       Template path (override configuration file)
                     --overwrite, -w      Overwrite existing scope
            ';


    public function __construct()
    {
        parent::__construct();

        $this->setHelp($this->help);

        $this->fs = new CraftsmanFileSystem();
    }

    public function handle()
    {
        $this->handleDebug();

        $scopeName = $this->argument('name');

        $overwrite = $this->option('overwrite');

        $data = [
            "name" => $scopeName,
            "className" => $scopeName,
            "overwrite" => $overwrite,
        ];

        $result = (new ScopeGenerator($this))->createFile();

        if (!$this->option('quiet')) {
            ($result["status"] === CraftsmanResultCodes::SUCCESS)
                ? Messenger::success("{$result["message"]}\n", "SUCCESS")
                : Messenger::error("{$result["message"]}\n", "ERROR");
        }

        return $result["status"];
    }
}
