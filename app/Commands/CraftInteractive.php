<?php

namespace App\Commands;

use Illuminate\Support\Str;
use App\Traits\CommandDebugTrait;
use Illuminate\Support\Facades\Artisan;
use LaravelZero\Framework\Commands\Command;
use Codedungeon\PHPMessenger\Facades\Messenger;

class CraftInteractive extends Command
{
    use CommandDebugTrait;

    protected $hidden = false;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'interactive
                                {--s|skip : Skip instructions}
    ';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Craft Interactive (build command using interactive interface)';

    protected $help = 'Craft Interactive
                     --skip, -s          Skip instructions
                ';

    function __construct()
    {
        parent::__construct();

        $this->setHelp($this->help);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->handleDebug();

        $skip = $this->option('skip');
        if (!$skip) {
            echo "\n";
            Messenger::success("Laravel Craftsman Wizard");
            Messenger::success("v" . config('app.version'));

            echo "\n";
            Messenger::note("The interactive wizard will prompt you for various options and parameters to guide you through crafting process.\nAt any time, you can press ctrl-c to abort.");

            echo "\n";
            Messenger::status('First you will select the type of resource you would like to craft', 'STEP 1:');
            echo "\n";
            Messenger::status('After selecting resource, you will be presented with a series of prompts to guide you through crafting process', 'STEP 2:');
            echo "\n";
            Messenger::status('After all parameters have been selected for desired resource, you will be shown the command to be executed', 'STEP 3:');
            echo "\n";
            Messenger::status('If the command is as you wish, you can the choose to execute the command and desired resource(s) will be created', 'STEP 4:');
            echo "\n";

            Messenger::info('At any time during process, you can exit process by pressing ctrl-c', 'NOTE');

            $result = $this->confirm("Would you like to continue?");
            if (!$result) {
                exit;
            }
        }

        // list of avaiable craftsman commands
        $commandList = [
            "API",
            "Class",
            "Controller",
            "Factory",
            "Form Request",
            "Migration",
            "Model",
            "Resource",
            "Seed",
            "Test",
            "Views",
        ];

        /**
         *   ->setForegroundColour('yellow')
         *   ->setBackgroundColour('black')
         */
        $command = $this->menu('Select the type of resource you would like to create', $commandList)
            ->setWidth(70)
            ->setBackgroundColour('240')
            ->setForegroundColour('cyan')
            ->open();

        $commandName = (!is_null($command)) ? Str::lower($commandList[$command]) : exit;

        $craftCommand = trim($this->buildCraftCommand($commandName));

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
            case "api":
                $craftCommand = $this->buildApiCommand();
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
                $craftCommand = $this->buildFormRequestCommand();
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

    private function buildApiCommand()
    {
        $commandName = "craft:api";

        $resource = $this->ask("esource Name (ie Contact, Post, Comment)");

        $model = trim($this->ask("Path to model [default: app/]"));
        if (strlen($model) > 0) {
            $model = "--model " . $model;
        }

        $tablename = $this->ask("Desired tablename [default to pluarlized resource name]", strtolower($this->getMigrationTablename($resource)));
        if (strlen($tablename) > 0) {
            $tablename = "--table " . $tablename;
        }

        $rows = trim($this->ask("Alternate number of rows to use in factory call"));
        if (strlen($rows) > 0) {
            $rows = "--rows " . $rows;
        }

        $current = $this->confirm("Use `--useCurrent` when creating migration ", "yes") ? '--current' : '';

        $noModel = $this->confirm("Craft model", "yes") ? '' : '--no-model';

        $noController = $this->confirm("Craft controller", "yes") ? '' : '--no-controller';

        $noFactory = $this->confirm("Craft factory", "yes") ? '' : '--no-fatory';

        $noMigration = $this->confirm("Craft migration", "yes") ? '' : '--no-migration';

        $noSeed = $this->confirm("Craft seed", "yes") ? '' : '--no-seed';

        $overwrite = $this->confirm("Would you like to overwrite api resources if they exists") ? '--overwrite' : '';

        $craftCommand = "{$commandName} {$resource} {$model} {$tablename} {$rows} {$current} {$noModel} {$noController} {$noFactory} {$noMigration} {$noSeed} {$overwrite}";

        return preg_replace('!\s+!', ' ', $craftCommand);
    }

    private function buildClassCommand()
    {
        $commandName = "craft:class";

        $resource = $this->ask("Class Name [You may use alternate path eg. `App/Services/MyService`]");

        $constructor = $this->confirm("Would you like to include constructor method") ? '--constructor' : '';

        $template = trim($this->ask("Template path (override configuration file)"));
        if (strlen($template) > 0) {
            $template = "--template " . $template;
        }

        $overwrite = $this->confirm("Would you like to overwrite class if it exists") ? '--overwrite' : '';

        $craftCommand = "{$commandName} {$resource} {$constructor} {$template} {$overwrite}";

        return preg_replace('!\s+!', ' ', $craftCommand);
    }

    private function buildControllerCommand()
    {
        $commandName = "craft:controller";

        $name = $this->ask("Controller Name");

        $model = trim($this->ask("Model path when creating controller (eg App/Models/Customer)"));
        if (strlen($model) > 0) {
            $model = "--model " . $model;
        }

        $api = $this->confirm("Create API controller (skips create and update methods") ? '--api' : '';

        $empty = $this->confirm("Create empty controller") ? '--empty' : '';

        $resource = $this->confirm("Create resource controller") ? '--resource' : '';

        $binding = $this->confirm("Include Route Model Binding") ? '--binding' : '';

        $collection = $this->confirm("Use Resource Collection") ? '--collection' : '';

        $template = trim($this->ask("Template path (override configuration file)"));
        if (strlen($template) > 0) {
            $template = "--template " . $template;
        }

        $overwrite = $this->confirm("Would you like to overwrite controller if it exists") ? '--overwrite' : '';

        $craftCommand = "{$commandName} {$name} {$model} {$api} {$empty} {$resource} {$collection} {$binding} {$template} {$overwrite}";

        return preg_replace('!\s+!', ' ', $craftCommand);
    }

    private function buildFactoryCommand()
    {
        $commandName = "craft:factory";

        $resource = $this->ask("Factory Name");

        $model = trim($this->ask("Model path when creating controller (eg App/Models/Customer)"));
        if (strlen($model) > 0) {
            $model = "--model " . $model;
        }

        $template = trim($this->ask("Template path (override configuration file)"));
        if (strlen($template) > 0) {
            $template = "--template " . $template;
        }

        $overwrite = $this->confirm("Would you like to overwrite factory if it exists") ? '--overwrite' : '';

        $craftCommand = "{$commandName} {$resource} {$model} {$template} {$overwrite}";

        return preg_replace('!\s+!', ' ', $craftCommand);
    }

    private function buildFormRequestCommand()
    {
        $commandName = "craft:request";

        $resource = $this->ask("Request Class Name");

        $rules = trim($this->ask("List of rules (eg. title?required|unique:posts|max:255,body?required)"));
        if (strlen($rules) > 0) {
            $rules = "--rules " . $rules;
        }

        $template = trim($this->ask("Template path (override configuration file)"));
        if (strlen($template) > 0) {
            $template = "--template " . $template;
        }

        $overwrite = $this->confirm("Would you like to overwrite form request if it exists") ? '--overwrite' : '';

        $craftCommand = "{$commandName} {$resource} {$rules} {$template} {$overwrite}";

        return preg_replace('!\s+!', ' ', $craftCommand);
    }

    private function buildMigrationCommand()
    {
        $commandName = "craft:migration";

        $resource = $this->ask("Migration Name (eg. create_posts_table *timestamp applied at creation)");

        // $model = trim($this->ask("Model path when creating migration (eg App/Models/Customer)"));
        // if (strlen($model) > 0) {
        //     $model = "--model " . $model;
        // }

        $tablename = $this->ask("Desired tablename", $this->getMigrationTablename($resource));
        if (strlen($tablename) > 0) {
            $tablename = "--table " . $tablename;
        }

        $fields = trim($this->ask("List of fields (eg. first_name:string@20:nullable, email:string@80:nullable:unique)"));
        if (strlen($fields) > 0) {
            $fields = "--fields " . $fields;
        }

        $foreign = trim($this->ask("Foreign Key Constraint (eg. post_id:posts,id)"));
        if (strlen($foreign) > 0) {
            $foreign = "--foreign " . $foreign;
        }

        $down = $this->confirm("Include down method in migration") ? '--down' : '';

        $template = trim($this->ask("Template path (override configuration file)"));
        if (strlen($template) > 0) {
            $template = "--template " . $template;
        }

        $craftCommand = "{$commandName} {$resource} {$tablename} {$fields} {$foreign} {$down} {$template}";

        return preg_replace('!\s+!', ' ', $craftCommand);
    }

    private function buildModelCommand()
    {
        // model options
        $collection = "";

        $commandName = "craft:model";

        $resource = $this->ask("Model Name [You may use alternate path eg. `App/Models/Contact`]");

        $tablename = $this->ask("Desired tablename", $this->getTablename($resource));

        $overwrite = $this->confirm("Would you like to overwrite model if it exists") ? '--overwrite' : '';

        $all = $this->confirm("Generate a migration, factory, and resource controller for the model");

        if ($all) {
            $all = "--all";
            $collection = $this->confirm("Use collections") ? "--collection" : "";
        }

        $template = trim($this->ask("Template path (override configuration file)"));
        if (strlen($template) > 0) {
            $template = "--template " . $template;
        }

        $overwrite = $this->confirm("Would you like to overwrite class if it exists") ? '--overwrite' : '';

        $craftCommand = "{$commandName} {$resource} --table {$tablename} {$all} {$collection} {$template} {$overwrite}";

        return preg_replace('!\s+!', ' ', $craftCommand);
    }

    private function buildResourceCommand()
    {
        $commandName = "craft:resource";

        $resource = $this->ask("Resource Name");

        $collection = $this->confirm("Create resource collection") ? '--collection' : '';

        $template = trim($this->ask("Template path (override configuration file)"));
        if (strlen($template) > 0) {
            $template = "--template " . $template;
        }

        $overwrite = $this->confirm("Would you like to overwrite resource if it exists") ? '--overwrite' : '';

        $craftCommand = "{$commandName} {$resource} {$collection} {$template} {$overwrite}";

        return preg_replace('!\s+!', ' ', $craftCommand);
    }

    private function buildSeedCommand()
    {
        $commandName = "craft:seed";

        $resource = $this->ask("Seed Name (eg ContactTableSeeder)");

        $defaultModel = str_replace("TableSeeder", "", $resource);
        $defaultModel = str_replace("Seeder", "", $defaultModel);

        $model = trim($this->ask("Model path when creating controller (eg App/Models/{$defaultModel})"));
        if (strlen($model) > 0) {
            $model = "--model " . $model;
        }

        $rows = trim($this->ask("Alternate number of rows to use in factory call"));
        if (strlen($rows) > 0) {
            $rows = "--rows " . $rows;
        }

        $template = trim($this->ask("Template path (override configuration file)"));
        if (strlen($template) > 0) {
            $template = "--template " . $template;
        }

        $overwrite = $this->confirm("Would you like to overwrite seed if it exists") ? '--overwrite' : '';

        $craftCommand = "{$commandName} {$resource} {$model} {$rows} {$template} {$overwrite}";

        return preg_replace('!\s+!', ' ', $craftCommand);
    }

    private function buildTestCommand()
    {
        $commandName = "craft:test";

        $resource = $this->ask("Test Class Name (eg ExampleTest)");

        $setupBlock = $this->confirm("Include `setUp` Block") ? '--setup' : '';

        $tearDownBlock = $this->confirm("Include `tearDown` Block") ? '--teardown' : '';

        $unit = $this->confirm("Create unit test (default Feature)") ? '--unit' : '';

        $template = trim($this->ask("Template path (override configuration file)"));
        if (strlen($template) > 0) {
            $template = "--template " . $template;
        }

        $overwrite = $this->confirm("Would you like to overwrite test if it exists") ? '--overwrite' : '';

        $craftCommand = "{$commandName} {$resource} {$setupBlock} {$tearDownBlock} {$unit} {$template} {$overwrite}";

        return preg_replace('!\s+!', ' ', $craftCommand);
    }

    private function buildViewsCommand()
    {
        $commandName = "craft:views";

        $resource = $this->ask("Resource name (resources/views/<name>)");

        $extends = $this->ask("Include `extends` block using supplied layout");
        if (strlen($extends) > 0) {
            $extends = "--extends " . $extends;
        }

        $section = $this->ask("Include `section` block using supplied name");
        if (strlen($section) > 0) {
            $section = "--section " . $section;
        }

        $noCreate = $this->confirm("Craft create view", "yes") ? '' : '--no-create';

        $noEdit = $this->confirm("Craft edit view", "yes") ? '' : '--no-edit';

        $noIndex = $this->confirm("Craft index view", "yes") ? '' : '--no-index';

        $noShow = $this->confirm("Craft show view", "yes") ? '' : '--no-show';

        $template = trim($this->ask("Template path (override configuration file)"));
        if (strlen($template) > 0) {
            $template = "--template " . $template;
        }

        $overwrite = $this->confirm("Would you like to overwrite test if it exists") ? '--overwrite' : '';

        $craftCommand = "{$commandName} {$resource} {$extends} {$section} {$noCreate} {$noEdit} {$noIndex} {$noShow} {$template} {$overwrite}";

        return preg_replace('!\s+!', ' ', $craftCommand);
    }

    private function getMigrationTablename($migrationName)
    {
        $parts = explode("_", $migrationName);
        if (sizeof($parts) >= 2) {
            return $parts[1];
        }

        return Str::plural($migrationName);
    }

    private function getTablename($model)
    {
        return Str::plural(strtolower(class_basename($model)));
    }
}
