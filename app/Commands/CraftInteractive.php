<?php

namespace App\Commands;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;
use LaravelZero\Framework\Commands\Command;
use Codedungeon\PHPMessenger\Facades\Messenger;

class CraftInteractive extends Command
{
    protected $hidden = false;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'interactive';

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
        // list of avaiable craftsman commands
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

        /**
         *   ->setForegroundColour('yellow')
         *   ->setBackgroundColour('black')
         */
        $command = $this->menu('Choose Command', $commandList)
            ->setWidth(60)
            ->open();

        $commandName = (!is_null($command)) ? Str::lower($commandList[$command]) : exit;

        $craftCommand = $this->buildCraftCommand($commandName);

        if (is_null($craftCommand)) {
            Messenger::warning("craft:{$commandName} incomplete", "STATUS");
        } else {
            if (strlen($craftCommand) > 1) {
                echo "\n";
                Messenger::info("{$craftCommand}\n", "READY");
                echo "\n";

                if ($this->confirm("Would you like to execute command now?", true)) {
                    Artisan::call($craftCommand);
                }
            } else {
                Messenger::error("Invalid `craft:{$commandName}` Command", "ERROR");
            }
        }
    }

    private function buildCraftCommand($commandName = "")
    {
        $craftCommand = "";

        switch ($commandName) {
            case "all":
                $craftCommand = $this->buildAllCommand();
                break;
            case "class":
                $craftCommand = $this->buildClassCommand();
                break;
            case "controller":
                $craftCommand = $this->buildControllerCommand();
                break;
            case "factory":
                $craftCommand = $this->buildFactoryCommand();
                break;
            case "form request":
                $craftCommand = $this->buildFormatRequestCommand();
                break;
            case "migration":
                $craftCommand = $this->buildMigrationCommand();
                break;
            case "model":
                $craftCommand = $this->buildModelCommand();
                break;
            case "resource":
                $craftCommand = $this->buildResourceCommand();
                break;
            case "seed":
                $craftCommand = $this->buildSeedCommand();
                break;
            case "test":
                $craftCommand = $this->buildTestCommand();
                break;
            case "views":
                $craftCommand = $this->buildViewsCommand();
                break;
            default:
                $craftCommand = "";
                break;
        }

        return $craftCommand;
    }
    private function buildAllCommand()
    {
        return null;
    }

    private function buildClassCommand()
    {
        $commandName = "craft:class";

        $resource = $this->ask("Class Name [You may use alternate path eg. `App/Services/MyService`]");

        $constructor = $this->confirm("Would you like to include constructor method") ? '--constructor' : '';

        $overwrite = $this->confirm("Would you like to overwrite resource if it exists") ? '--overwrite' : '';

        $template = $this->ask("Template path (override configuration file)");
        if (strlen($template) > 0) {
            $template = "--template " . $template;
        }

        $craftCommand = "{$commandName} {$resource} {$constructor} {$template} {$overwrite}";

        return str_replace("  ", " ", $craftCommand);
    }

    private function buildControllerCommand()
    {
        $commandName = "craft:controller";

        $name = $this->ask("Controller Name");

        $model = $this->ask("Use <model> when creating controller");
        if (strlen($model) > 0) {
            $model = "--model " . $model;
        }

        $api = $this->confirm("Create API controller (skips create and update methods") ? '--api' : '';

        $empty = $this->confirm("Create empty controller") ? '--empty' : '';

        $resource = $this->confirm("Create resource controller") ? '--resource' : '';

        $binding = $this->confirm("Include Route Model Binding") ? '--binding' : '';

        $collection = $this->confirm("Use Resource Collection") ? '--collection' : '';

        $template = $this->ask("Template path (override configuration file)");
        if (strlen($template) > 0) {
            $template = "--template " . $template;
        }

        $overwrite = $this->confirm("Would you like to overwrite resource if it exists") ? '--overwrite' : '';

        $craftCommand = "{$commandName} {$name} {$model} {$api} {$empty} {$collection} {$binding} {$template} {$overwrite}";

        return str_replace("  ", " ", $craftCommand);
    }

    private function buildFactoryCommand()
    {
        return null;
    }

    private function buildFormatRequestCommand()
    {
        return null;
    }

    private function buildMigrationCommand()
    {
        return null;
    }

    private function buildModelCommand()
    {
        // model options
        $collection = "";

        $commandName = "craft:model";

        $resource = $this->ask("Model Name [You may use alternate path eg. `App/Models/Contact`]");

        $tablename = $this->ask("Desired tablename", $this->getTablename($resource));

        $overwrite = $this->confirm("Would you like to overwrite resource if it exists") ? '--overwrite' : '';

        $all = $this->confirm("Generate a migration, factory, and resource controller for the model");

        if ($all) {
            $all = "--all";
            $collection = $this->confirm("Use collections") ? "--collection" : "";
        }

        $template = $this->ask("Template path (override configuration file)");
        if (strlen($template) > 0) {
            $template = "--template " . $template;
        }

        $craftCommand = "{$commandName} {$resource} --tablename {$tablename} {$all} {$collection} {$template} {$overwrite}";

        return str_replace("  ", " ", $craftCommand);
    }

    private function buildResourceCommand()
    {
        return null;
    }

    private function buildSeedCommand()
    {
        return null;
    }

    private function buildTestCommand()
    {
        return null;
    }

    private function buildViewsCommand()
    {
        return null;
    }

    private function getTablename($model)
    {
        return Str::plural(strtolower(class_basename($model)));
    }
}
