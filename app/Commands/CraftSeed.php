<?php

namespace App\Commands;

use App\CraftsmanFileSystem;
use Exception;
use LaravelZero\Framework\Commands\Command;

/**
 * Class CraftSeed
 * @package App\Commands
 */
class CraftSeed extends Command
{
    /**
     * @var CraftsmanFileSystem
     */
    protected $fs;
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'craft:seed 
                                {name : Seed name} 
                                {--m|model= : Associated model} 
                                {--r|rows= : Alternate number of rows to use in factory call}
                                {--t|template= : Template path (override configuration file)}
                                {--w|overwrite : Overwrite existing seed}
                            ';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Craft Seed
                     <name>               Seed Name
                     --model, -m          Path to model
                     --rows, -r           Number of rows to use in factory call (Optional)
                     --template, -t       Template path (override configuration file)
                     --overwrite, -w      Overwrite existing seed
            ';

    /**
     * CraftSeed constructor.
     */
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
            $overwrite = $this->option('overwrite');

            if (strlen($model) === 0) {
                $this->error("Must supply model name");
            } else {
                $num_rows = (int) $this->option('rows') ?: 1;
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
