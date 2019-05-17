<?php

namespace App\Commands;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;
use LaravelZero\Framework\Commands\Command;
use Codedungeon\PHPMessenger\Facades\Messenger;

class CraftInteractive extends Command
{

    protected $hidden = true;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'craft:interactive';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Craft Interactive (build command using interactive interface)';

    function __construct()
    {
        parent::__construct();
    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $commandList = [
            "All",
            "Class",
            "Controller",
            "Factory",
            "Form Request",
            "Migration",
            "Model",
            "Resource",
            "Seed",
            "Test",
            "Views"
        ];

        $command = $this->menu('Choose Command', $commandList)
            ->setForegroundColour('black')
            ->setBackgroundColour('250')
            ->setWidth(40)
            ->open();

        $commandName = Str::lower($commandList[$command]);

        $craftCommand = "";
        switch ($commandName) {
            case "model":
                $craftCommand = $this->buildModelCommand();
                break;
            case "class":
                $craftCommand = $this->buildClassCommand()();
                break;
            default:
                $craftCommand = "";
                break;
        }

        if (strlen($command) > 1) {
            Messenger::info("{$craftCommand}\n", "COMMAND");

            if ($this->confirm("Would you like to execute command now")) {
                Artisan::call($craftCommand);
            }
        } else {
            Messenger::error("Invalid `craft:{$commandName}` Command", "ERROR");
        }
    }

    private function buildModelCommand()
    {
        $commandName = "craft:model";

        $resource = $this->ask("{$commandName} Resource Name");

        $tablename = $this->ask("Desired tablename");

        $overwrite = $this->confirm("Would you like to overwrite resource if it exists") ? '--overwrite' : '';

        $all = $this->confirm("Generate a migration, factory, and resource controller for the model");
        $collection = "";
        if ($all) {
            $all = "--all";
            $collection = $this->confirm("Use collections (only used when --all supplied)");
            if ($collection) {
                $collection = "--collection";
            }
        }

        $template = $this->ask("Template path (override configuration file)");
        if (strlen($template) > 0) {
            $template = "--template " . $template;
        }

        $craftCommand = "{$commandName} {$resource} --tablename {$tablename} {$all} {$collection} {$template} {$overwrite}";

        return $craftCommand;
    }

    private function buildClassCommand()
    {
        $commandName = "craft:class";
    }
}
