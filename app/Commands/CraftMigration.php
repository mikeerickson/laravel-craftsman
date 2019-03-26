<?php

namespace App\Commands;

use App\CraftsmanFileSystem;
use Carbon\Carbon;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;

class CraftMigration extends Command
{
    protected $fs;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'craft:migration 
                                {name : Migration name (timestamp applied at creation)} 
                                {--t|table= : Desired tablename} 
                                {--m|model= : Path to migration model} 
                                {--f|fields= : List of fields (optional)} 
                                {--d|down : Include down method in migration}
                            ';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Crafts Migration <name> [options]';

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
        // timestamp to be prepended to name
        $dt = Carbon::now()->format('Y_m_d_His');

        $migrationName = $this->argument('name');
        $model = $this->option('model');
        $down = $this->option('down');

        if (strlen($model) === 0) {
            $this->error("Must supply model name");
        } else {
            $tablename = $this->option('table');
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
                "down" => $down,
            ];

            $migrationFilename = $dt."_".$migrationName;
            $result = $this->fs->createFile('migration', $migrationFilename, $data);

            if (getenv("APP_ENV") === "testing") {
                $this->info($result["message"]);
            } else {
                echo "\n";
                $result["status"]
                    ? $this->info($result["message"])
                    : $this->error($result["message"]);
            }
        }
    }
}
