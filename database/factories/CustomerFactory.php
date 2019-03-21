<?php

use App\Models\Customer;
use Faker\Generator as Faker;

$factory->define(Customer::class, function (Faker $faker) {
    return [
        'key' => Str::slug($faker->sentence),
        'value' => $faker->sentence,
    ];
});
