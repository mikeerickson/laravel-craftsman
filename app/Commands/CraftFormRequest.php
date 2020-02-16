<?php

namespace App\Commands;

use App\CraftsmanFileSystem;
use App\Traits\CommandDebugTrait;
use LaravelZero\Framework\Commands\Command;

/**
 * Class CraftClass
 * @package App\Commands
 */
class CraftFormRequest extends Command
{
    use CommandDebugTrait;

    protected $signature = 'craft:request
                                {name : Class name}
                                {--r|rules= : List of rules (optional)}
                                {--t|template= : Template path (override configuration file)}
                                {--w|overwrite   : Overwrite existing class}
                                {--d|debug   : Use Debug Interface}
                            ';

    protected $description = 'Craft Form Request';

    protected $help = 'Craft Form Request
                     <name>               Class Name
                     --rules, -r          List of rules (optional)
                       eg. --rules title?required|unique:posts|max:255,body?required

                     --template, -t       Path to custom template (override config file)
                     --overwrite, -w      Overwrite existing class
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

        $className = $this->argument('name');
        $rules = $this->option('rules');

        $data = [
            'name'      => $className,
            'template'  => $this->option('template'),
            'overwrite' => $this->option('overwrite'),
            'rules' => $rules,
        ];

        $this->fs->createFile('request', $className, $data);
    }
}
