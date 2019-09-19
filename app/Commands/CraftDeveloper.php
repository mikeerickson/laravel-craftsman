<?php

namespace App\Commands;

use App\CraftsmanFileSystem;
use Illuminate\Support\Arr;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use Codedungeon\PHPMessenger\Facades\Messenger;

class CraftDeveloper extends Command
{
    protected $hidden = true;

    protected $signature = 'craft:developer';

    protected $description = 'Verify Templates';

    public function handle()
    {
        echo "\n";

        Messenger::debug("cwd: " . getcwd());

        $fs = new CraftsmanFileSystem();
        $value = $fs->getConfigValue("craftsman.paths.class");
        Messenger::info($value);

        $value = $fs->getConfigValue("full.fname");
        Messenger::info($value);

        exit;

        //        $path = Phar::running(false);
        //        if (strlen($path) > 0) {
        //            $path = dirname($path).DIRECTORY_SEPARATOR;
        //        }

        $templates = config('craftsman.templates');
        foreach ($templates as $template) {
            $template = getcwd() . DIRECTORY_SEPARATOR . $template;
            echo ($template . ' templates exists: ' . file_exists($template) . PHP_EOL);
        }
    }

    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
