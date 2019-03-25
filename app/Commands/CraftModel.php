<?php

namespace App\Commands;

use App\CraftsmanFileSystem;
use Codedungeon\PHPMessenger\Facades\Messenger;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;

class CraftModel extends Command
{

    protected $fs;
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'craft:model {name : Model name} {--t|table= : Tablename if different than model name}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Crafts model <name>';

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
        $modelName = $this->argument('name');
        $parts = explode("/", $modelName);
        $model = array_pop($parts);
        $namespace = count($parts) > 0 ? implode($parts, "\\") : "App";

        $tablename = $this->option("table");
        if (strlen($tablename) === 0) {
            $tablename = Str::plural(strtolower($model));
        }
        $data = [
            "model" => $model,
            "name" => $modelName,
            "tablename" => $tablename,
            "namespace" => $namespace,
        ];

        $result = $this->fs->createFile('model', $modelName, $data);

        if (getenv("APP_ENV") === "testing") {
            $this->info($result["message"]);
        } else {
            echo "\n";
            $result["status"]
                ? Messenger::success($result["message"], "SUCCESS")
                : Messenger::error($result["message"], "ERROR");
        }
    }
}
