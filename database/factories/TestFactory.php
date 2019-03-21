<?php

use App\Models\Test;
use Faker\Generator as Faker;

$factory->define(Test::class, function (Faker $faker) {
    return [
        'key' => Str::slug($faker->sentence),
        'value' => $faker->sentence,
    ];
});
