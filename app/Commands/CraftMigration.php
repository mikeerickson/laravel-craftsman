<?php

namespace App\Commands;

use App\CraftsmanFileSystem;
use Carbon\Carbon;
use Codedungeon\PHPMessenger\Facades\Messenger;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;

/**
 * Class CraftMigration
 * @package App\Commands
 */
class CraftMigration extends Command
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
    protected $signature = 'craft:migration 
                                {name : Migration name (timestamp applied at creation)} 
                                {--m|model= : Path to migration model (required)} 
                                {--t|tablename= : Desired tablename} 
                                {--f|fields= : List of fields (optional)} 
                                {--d|down : Include down method in migration}
                                {--w|overwrite : Overwrite migration (default: true)}
                            ';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Craft Migration
                     <name>               Migration Name (will be appended with timestamp)
                     --model, -m          Path to model (required)
                     --tablename, -t      Desired tablename
                     --fields, -f         List of fields (optional)
                                           eg. --fields first_name:string@20:nullable, email:string@80:nullable:unique
                     --down, -d           Include down methods (skipped by default)
                     --overwrite, -w      Overwrite migration (skipped by default, default: true)
            ';

    /**
     * CraftMigration constructor.
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
        $migrationName = $this->argument('name');
        $model = $this->option('model');

        if (strlen($model) === 0) {
            Messenger::log("");
            Messenger::error("Migrations require model name (--model)\n", "ERROR");
            return;
        } else {
            $tablename = $this->option('tablename');
            $fields = $this->option('fields');

            if (strlen($tablename) === 0) {
                $parts = explode("/", $model);
                $tablename = Str::plural(array_pop($parts));
            }
            $data = [
                "model" => $model,
                "name" => $migrationName,
                "tablename" => $tablename,
                "fields" => $fields,
                "down" => $this->option('down'),
                "overwrite" => $this->option('overwrite'),
            ];

            // timestamp to be prepended to name
            $dt = Carbon::now()->format('Y_m_d_His');
            $migrationFilename = $dt."_".$migrationName;

            $this->fs->createFile('migration', $migrationFilename, $data);
        }
    }
}
