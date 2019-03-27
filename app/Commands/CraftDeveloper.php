<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use Phar;

class CraftDeveloper extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'craft:developer';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Verify Templates';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $path = Phar::running(false);
        if (strlen($path) > 0) {
            $path = dirname($path).DIRECTORY_SEPARATOR;
        }

        $templates = config('craftsman.templates');
        foreach ($templates as $template) {
            $template = $path.$template;
            echo($template.' templates exists: '.file_exists($template).PHP_EOL);
        }
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
