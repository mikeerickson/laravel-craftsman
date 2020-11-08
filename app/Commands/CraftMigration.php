<?php

namespace App\Commands;

use App\Traits\CommandDebugTrait;
use App\Generators\MigrationGenerator;
use LaravelZero\Framework\Commands\Command;
use Codedungeon\PHPMessenger\Facades\Messenger;

/**
 * Class CraftMigration
 * @package App\Commands
 */
class CraftMigration extends Command
{
    use CommandDebugTrait;

    protected $signature = 'craft:migration
                                {name : Migration name (timestamp applied at creation)}
                                {--m|model= : Path to migration model (required)}
                                {--t|table= : Desired tablename}
                                {--c|update : Create update (change) migration}
                                {--f|fields= : List of fields (optional)}
                                {--r|foreign= : Add constraint (optional)}
                                {--p|pivot= : Create foreign references (optional)}
                                {--u|current : Use --useCurrent for timestamps}
                                {--d|down : Include down method in migration}
                                {--a|template= : Template path (override configuration file)}
                                {--b|debug   : Use Debug Interface}
                            ';


    protected $description = "Craft Database Migration";

    protected $help = 'Craft Migration
                     <name>               Migration Name (will be appended with timestamp)
                     --model, -m          Path to model (required)
                     --table, -t          Desired tablename
                     --update, -c         Create update (change) migration
                     --fields, -f         List of fields (optional)
                                           eg. --fields "first_name:string@20:nullable, email:string@80:nullable:unique"
                     --foreign, -r        Add constraint (skipped by default)
                     --pivot, -p          Add pivot table information [composite unique, foreign fields] (skipped by default)
                                           eg. --pivot="contacts,tags" (will use contact_id, tag_id)
                                           eg. --pivot="index:false;contacts,tags" (no index; use contact_id, tag_id)
                                           eg. --pivot="contacts:my_contact_id,tags:my_tag_id" (defined table:field)
                                           Note: cascade on delete will always be added automatically
                     --current, -u        Use --useCurrent for timestamps (skipped by default)
                     --down, -d           Include down methods (skipped by default)

                     --template, -a       Template path (override configuration file)lc

                     ========================================================================================================
                     Note: --overwrite flag is not supported as all migrations have current timestamp in filename
                     ========================================================================================================
            ';


    public function __construct()
    {
        parent::__construct();

        $this->setHelp($this->help);
    }

    public function handle()
    {
        $this->handleDebug();

        $result = (new MigrationGenerator($this))->createFile();

        if (!$this->option('quiet')) {
            if ($result["status"] === CraftsmanResultCodes::SUCCESS) {
                Messenger::success("{$result["shortenFilename"]} created successfully\n", "SUCCESS");
            }

            if ($result["status"] === CraftsmanResultCodes::FAIL) {
                Messenger::error("{$result["message"]}\n", "ERROR");
            }
        }

        return $result["status"];
    }
}
