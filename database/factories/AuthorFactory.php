<?php

use App\Models\Author;
use Faker\Generator as Faker;

$factory->define(Author::class, function (Faker $faker) {
    return [
        'key' => Str::slug($faker->sentence),
        'value' => $faker->sentence,
    ];
});
