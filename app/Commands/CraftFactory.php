<?php

namespace App\Commands;

use App\CraftsmanFileSystem;
use LaravelZero\Framework\Commands\Command;

class CraftFactory extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'craft:factory 
                                {name : Factory Name} 
                                {--m|model= : Associated model}
                            ';
    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Craft Factory
                     <name>               Factory Name
                     --model, -m          Use <model> when creating controller
            ';

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
        $tablename = "";
        $factoryName = $this->argument('name');
        $model = $this->option('model');
        if (strlen($model) === 0) {
            $this->error("Must supply model name");
        } else {
            $data = [
                "model" => $model,
                "name" => $factoryName,
            ];

            $result = $this->fs->createFile('factory', $factoryName, $data);

            if (getenv("APP_ENV") === "testing") {
                $this->info($result["message"]);
            } else {
                echo "\n";
                $result["status"]
                    ? $this->info("✔︎ ".$result["message"])
                    : $this->error($result["message"]);
            }
        }
    }
}
