<?php

namespace App\Commands;

use Exception;
use App\CraftsmanFileSystem;
use LaravelZero\Framework\Commands\Command;

/**
 * Class CraftSeed
 * @package App\Commands
 */
class CraftSeed extends Command
{
    protected $fs;

    protected $signature = 'craft:seed
                                {name : Seed name}
                                {--m|model= : Associated model}
                                {--r|rows= : Alternate number of rows to use in factory call}
                                {--t|template= : Template path (override configuration file)}
                                {--w|overwrite : Overwrite existing seed}
                            ';

    protected $description = "Craft Seed";

    protected $help = 'Craft Seed
                     <name>               Seed Name
                     --model, -m          Path to model
                     --rows, -r           Number of rows to use in factory call (Optional)
                     --template, -t       Template path (override configuration file)
                     --overwrite, -w      Overwrite existing seed
            ';

    public function __construct()
    {
        parent::__construct();

        $this->fs = new CraftsmanFileSystem();

        $this->setHelp($this->help);
    }

    public function handle()
    {
        try {
            $seedName = $this->argument('name');
            $model = $this->option('model');
            $overwrite = $this->option('overwrite');

            if (strlen($model) === 0) {
                $this->error("Must supply model name");
            } else {
                $num_rows = (int)$this->option('rows') ?: 1;
                $data = [
                    "model" => $model,
                    "name" => $seedName,
                    "num_rows" => $num_rows,
                    "overwrite" => $overwrite,
                    "template" => $this->option('template'),
                ];

                $this->fs->createFile('seed', $seedName, $data);
            }
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
