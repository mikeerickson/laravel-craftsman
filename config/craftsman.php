<?php

return [

    /*
     |--------------------------------------------------------------------------
     | Craftsman Paths
     |--------------------------------------------------------------------------
     |
     | This value determines the "paths" which will be used when crafting assets
     | In most cases, these are the sensible defaults used by Laravel.
     | You might want to adjust the `models` path to something like `app/Models`.
     */

    'paths' => [
        'class' => 'app',
        'controllers' => 'app/Http/Controllers',
        'factories' => 'database/factories',
        'migrations' => 'database/migrations',
        'models' => 'app',
        'seeds' => 'database/seeds'
    ],

    /*
     |--------------------------------------------------------------------------
     | Templates
     |--------------------------------------------------------------------------
     |
     | This value determines the default template paths for each asset
     | If you wish to override any of these templates, you should create them
     | in a separate directory (located in app directory) and update the path
     | accordingly. You should not modify the default templates directly as they
     | will be overwritten during any updates..
     */

    'templates' => [
        'class' => 'app/templates/class.mustache.php',
        'api-controller' => 'app/templates/api-controller.mustache.php',
        'empty-controller' => 'app/templates/empty-controller.mustache.php',
        'controller' => 'app/templates/controller.mustache.php',
        'factory' => 'app/templates/factory.mustache.php',
        'migration' => 'app/templates/migration.mustache.php',
        'model' => 'app/templates/model.mustache.php',
        'seed' => 'app/templates/seed.mustache.php',
    ],

];
