<?php

namespace App\Commands;

use App\CraftsmanFileSystem;
use Codedungeon\PHPMessenger\Facades\Messenger;
use LaravelZero\Framework\Commands\Command;

class CraftClass extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'craft:class 
                                {name : Class name} 
                                {--c|constructor= : Include constructor method}
                            ';
    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Craft Class
                     <name>               Class Name
                     --constructor, -c    Include constructor method
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
        $className = $this->argument('name');

        $data = [
            "name" => $className,
        ];

        $result = $this->fs->createFile('class', $className, $data);

        if (getenv("APP_ENV") === "testing") {
            $this->info($result["message"]);
        } else {
            echo "\n";
            Messenger::success($result["message"], "SUCCESS");
        }
    }
}
