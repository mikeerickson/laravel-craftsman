<?php

namespace App\Commands;

use App\CraftsmanFileSystem;
use LaravelZero\Framework\Commands\Command;

class CraftController extends Command
{

    protected $fs;
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'craft:controller {name} {--m|model=}';
    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'craft:controller <name>';

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
        $controllerName = $this->argument('name');
        $model = $this->option('model');
        if (strlen($model) === 0) {
            $this->error("Must supply model name");
        } else {
            $data = [
                "model" => $model,
                "name" => $controllerName,
            ];
            $result = $this->fs->createFile('controller', $controllerName, $data);
            $this->info($result["message"]);
        }
    }
}
