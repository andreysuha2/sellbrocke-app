<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Defect;
use Faker\Generator as Faker;

$factory->define(Defect::class, function (Faker $faker) {
    return [
        "name" => $faker->word(),
        "price_reduction" => $faker->randomFloat(2, 1, 99.99),
        "description" => $faker->text(200)
    ];
});
