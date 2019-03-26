<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class TestTemplates extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'craft:templates';

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
        $templates = config('craftsman.templates');
        foreach ($templates as $template) {
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
