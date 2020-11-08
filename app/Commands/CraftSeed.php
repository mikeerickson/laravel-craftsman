<?php

namespace App\Commands;

use Exception;
use App\CraftsmanFileSystem;
use App\Traits\CommandDebugTrait;
use Illuminate\Support\Facades\Artisan;
use LaravelZero\Framework\Commands\Command;
use Codedungeon\PHPMessenger\Facades\Messenger;

/**
 * Class CraftSeed
 * @package App\Commands
 */
class CraftSeed extends Command
{
    use CommandDebugTrait;

    protected $fs;

    protected $signature = 'craft:seed
                                {name : Seed name}
                                {--m|model= : Associated model}
                                {--f|factory : Generate Factory (if not already created)}
                                {--r|rows= : Alternate number of rows to use in factory call}
                                {--t|template= : Template path (override configuration file)}
                                {--w|overwrite : Overwrite existing seed}
                                {--d|debug   : Use Debug Interface}
                            ';

    protected $description = "Craft Seed";

    protected $help = 'Craft Seed
                     <name>               Seed Name
                     --model, -m          Path to model
                     --factory, -f        Generate Factory (if not already created)
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
        $this->handleDebug();

        try {
            $seedName = $this->argument('name');
            $model = $this->option('model');
            $factory = $this->option('factory');
            $overwrite = $this->option('overwrite');

            if (strlen($model) === 0) {
                $this->error("Must supply model name");
            } else {
                $num_rows = (int) $this->option('rows') ?: 1;
                $data = [
                    "model" => $model,
                    "name" => $seedName,
                    "factory" => $factory,
                    "num_rows" => $num_rows,
                    "overwrite" => $overwrite,
                    "template" => $this->option('template'),
                ];

                $result = $this->fs->createFile('seed', $seedName, $data);

                return $result["status"];
            }
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
