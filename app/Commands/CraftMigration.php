<?php

namespace App\Commands;

use Carbon\Carbon;
use Illuminate\Support\Str;
use App\CraftsmanFileSystem;
use LaravelZero\Framework\Commands\Command;

/**
 * Class CraftMigration
 * @package App\Commands
 */
class CraftMigration extends Command
{
    protected $fs;

    protected $signature = 'craft:migration
                                {name : Migration name (timestamp applied at creation)}
                                {--m|model= : Path to migration model (required)}
                                {--t|tablename= : Desired tablename}
                                {--f|fields= : List of fields (optional)}
                                {--c|foreign= :     Add constraint (optional)}
                                {--u|current :        Use --useCurrent for timestamps}
                                {--d|down : Include down method in migration}
                                {--p|template= : Template path (override configuration file)}
                            ';

    protected $description = "Craft Database Migration";

    protected $help = 'Craft Migration
                     <name>               Migration Name (will be appended with timestamp)
                     --model, -m          Path to model (required)
                     --tablename, -t      Desired tablename
                     --fields, -f         List of fields (optional)
                                           eg. --fields first_name:string@20:nullable, email:string@80:nullable:unique
                     --foreign, -c        Add constraint (skipped by default)
                     --current, -u        Use --useCurrent for timestamps (skipped by default)
                     --down, -d           Include down methods (skipped by default)

                     --template, -p       Template path (override configuration file)

                     ============================================================================================
                     Note: --overwrite flag is not supported as all migrations have current timestamp in filename
                     ============================================================================================
            ';

    /**
     * CraftMigration constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->fs = new CraftsmanFileSystem();

        $this->setHelp($this->help);
    }

    public function handle()
    {
        $migrationName = $this->argument('name');
        $model = $this->option('model');
        $tablename = $this->option('tablename');
        $fields = $this->option('fields');
        $foreign = $this->option('foreign');

        if (strlen($tablename) === 0 || (is_null($tablename))) {
            if (strlen($model) === 0) {
                $parts = explode("_", $migrationName);
                array_shift($parts);
                array_pop($parts);
                $tablename = implode("_", $parts);
                $model = str_replace("_", "", Str::title($tablename));
            } else {
                $parts = explode("/", $model);
                $tablename = Str::plural(array_pop($parts));
            }
        } else {
            if (strlen($model) === 0) {
                $model = str_replace("_", "", Str::title($tablename));
            }
        }

        $create = true;
        $update = false;
        $resourceParts = explode("_", $migrationName);
        if (sizeof($resourceParts) >= 1) {
            if ($resourceParts[0] === 'update') {
                $update = true;
                $create = false;
            }
        }

        $data = [
            "model" => $model,
            "name" => $migrationName,
            "tablename" => $tablename,
            "fields" => $fields,
            "foreign" => $foreign,
            "down" => $this->option('down'),
            "current" => $this->option('current') || config('craftsman.miscellaneous.useCurrentDefault'),
            "create" => $create,
            "update" => $update
        ];

        // timestamp to be prepended to name
        $dt = Carbon::now()->format('Y_m_d_His');
        $migrationFilename = $dt . "_" . $migrationName;

        $this->fs->createFile('migration', $migrationFilename, $data);
    }
}
