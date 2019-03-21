<?php

namespace App\Commands;

use App\CraftsmanFileSystem;
use LaravelZero\Framework\Commands\Command;

class CraftClass extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'craft:class {name} {--c|constructor=}';
    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Crafts Class <name> [options]';

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
        $this->info($result["message"]);
    }
}
