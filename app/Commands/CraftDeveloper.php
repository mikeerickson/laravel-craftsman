<?php

namespace App\Commands;

use App\CraftsmanFileSystem;
use LaravelZero\Framework\Commands\Command;

class CraftDeveloper extends Command
{
    protected $hidden = true;

    protected $signature = 'craft:developer';

    protected $description = 'Verify Templates';

    public function handle()
    {
        $fs = new CraftsmanFileSystem();

        debug("cwd: " . getcwd());
        debug("local config: " . $fs->getLocalConfigFilename());

        debug("config path: " . config_path());

        $useCurrentDefault = $fs->getConfigValue("miscellaneous.useCurrentDefault");
        debug("useCurrentDefault: {$useCurrentDefault}");

        // echo "\nTemplates:\n";
        // $templates = $fs->getConfigValue('templates');

        // foreach ($templates as $key => $template) {
        //     $template = getcwd() . DIRECTORY_SEPARATOR . $template;
        //     $exists = file_exists($template) ? ' exists' : ' does not exist';
        //     echo ($template . $exists . PHP_EOL);
        // }

        // echo "\nPaths:\n";
        // $paths = $fs->getConfigValue('paths');
        // foreach ($paths as $key => $path) {
        //     $path = getcwd() . DIRECTORY_SEPARATOR . $path;
        //     echo (str_pad($key, 12) . " => " . $path . PHP_EOL);
        // }
    }
}
