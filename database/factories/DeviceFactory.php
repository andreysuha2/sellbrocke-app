<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Device;
use Faker\Generator as Faker;

$factory->define(Device::class, function (Faker $faker) {
    return [
        "name" => $faker->word,
        "slug" => $faker->unique()->word(),
        "company_id" => rand(1, 8),
        "base_price" => $faker->randomFloat(2, 1),
        "description" => $faker->text(200)
    ];
});
