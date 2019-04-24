<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class CraftDeveloper extends Command
{
    protected $hidden = true;

    protected $signature = 'craft:developer';

    protected $description = 'Verify Templates';

    public function handle()
    {
        var_dump("cwd: ".getcwd());

//        $path = Phar::running(false);
//        if (strlen($path) > 0) {
//            $path = dirname($path).DIRECTORY_SEPARATOR;
//        }

        $templates = config('craftsman.templates');
        foreach ($templates as $template) {
            $template = getcwd().DIRECTORY_SEPARATOR.$template;
            echo($template.' templates exists: '.file_exists($template).PHP_EOL);
        }
    }

    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
