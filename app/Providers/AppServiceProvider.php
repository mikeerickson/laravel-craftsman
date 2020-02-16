<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        config([
            'logging.channels.single.path' => \Phar::running()
                ? dirname(\Phar::running(false)) . '/logs/laravel-craftsman.log'
                : storage_path('logs/laravel-craftsman.log')
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
