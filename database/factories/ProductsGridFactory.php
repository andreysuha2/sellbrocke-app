<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ProductGrid;
use Faker\Generator as Faker;

$factory->define(ProductGrid::class, function (Faker $faker) {
    $type = $faker->randomElements([ "carrier", "size" ], 1)[0];

    return [
        "name" => $faker->word,
        "slug" => $faker->word . "_" . $faker->unique()->numberBetween(1000, 1000000),
        "type" => $faker->randomElements([ "carrier", "size" ], 1)[0]
    ];
});
