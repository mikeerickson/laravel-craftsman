<?php

namespace App\Commands;

use App\CraftsmanFileSystem;
use LaravelZero\Framework\Commands\Command;

class CraftViews extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'craft:views 
                                {name : Resource name (resources/views/<name>)} 
                                {--x|extends= : Include extends block using supplied layout}
                                {--s|section= : Include section block using supplied name}
                                {--c|no-create : Don\'t craft create view}
                                {--d|no-edit : Don\'t craft edit view}
                                {--i|no-index : Don\'t craft index view}
                                {--w|no-show : Don\'t craft show view}
                            ';
    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Craft Views
                     <name>               Resource name (resources/views/<name>)
                     --extends, -e        Include extends block using supplied layout
                     --section, -s        Include section block using supplied section name
                     --no-create, -c      Exclude create view
                     --no-edit, -d        Exclude edit view
                     --no-index, -i       Exclude index view
                     --no-show, -w        Exclude show view
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
        $assetName = $this->argument('name');
        $extends = $this->option("extends");
        $section = $this->option("section");

        $noCreate = $this->option('no-create');
        $noEdit = $this->option('no-edit');
        $noIndex = $this->option('no-index');
        $noShow = $this->option('no-show');

        $data = [
            "name" => $assetName,
            "extends" => $extends,
            "section" => $section,
            "noCreate" => $noCreate,
            "noEdit" => $noEdit,
            "noIndex" => $noIndex,
            "noShow" => $noShow,
        ];

        $result = $this->fs->createViewFiles($assetName, $data);

        if (getenv("APP_ENV") === "testing") {
            $this->info($result["message"]);
        } else {
            echo "\n";
            $this->info("✔︎ resources/views/{$assetName} {{$result["message"]}} Created Successfully");
        }
    }
}
