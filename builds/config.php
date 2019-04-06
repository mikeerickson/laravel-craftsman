<?php

/*
 |---------------------------------------------------------------------------------------
 | Craftsman User Templates
 |---------------------------------------------------------------------------------------
 |
 | This value will be used by Laravel Craftsman to read if there are any template
 | overrides. You can customize any one of the following templates by defining the
 | pathname to custom templates (see below for an example).
 |
 | NOTE: You can store custom templates anywhere you wish, however, it is recommended
 |       that you keep them in the same directory as default templates at
 |       macOS / linux
 |         '/Users/<name>/.composer/vendor/codedungeon/laravel-craftsman/'
 |       Windows
 |
 |   class:            path to template which will be used when creating class
 |   controller:       path to template which will be used when creating controller
 |   api-controller:   path to template which will be used when creating api-controller
 |   empty-controller: path to template which will be used when creating empty-controller
 |   factory:          path to template which will be used when creating factory
 |   migration:        path to template which will be used when creating migration
 |   model:            path to template which will be used when creating model
 |   seed:             path to template which will be used when creating seed
 */

return [
    "templates" => [
        // example
        // 'class' => 'templates/class.user.mustache',
        "sample" => "templates/sample.user.mustache",
    ],
];
