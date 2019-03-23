<?php

namespace App\Commands;

use App\CraftsmanFileSystem;
use Exception;
use LaravelZero\Framework\Commands\Command;

class CraftSeed extends Command
{
    protected $fs;
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'craft:seed {name : Seed name} {--m|model= : Associated model} {--r|rows= : Alternate number of rows to user in factory call}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Crafts seed <name>';

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
        try {
            $seedName = $this->argument('name');
            $model = $this->option('model');
            if (strlen($model) === 0) {
                $this->error("Must supply model name");
            } else {
                $num_rows = (int) $this->option('rows') ?: 1;
                $data = [
                    "model" => $model,
                    "name" => $seedName,
                    "num_rows" => $num_rows,
                ];

                $result = $this->fs->createFile('seed', $seedName, $data);
                $this->info($result["message"]);
            }
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }

    }
}
