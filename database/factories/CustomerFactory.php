<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(App\Models\Customer::class, function (Faker $faker) {
    return [
        "merchant_id" => 1,
        "merchant_customer_id" => $faker->unique()->numberBetween(1, 100000),
        "email" => $faker->email,
        "login" => $faker->name,
        "first_name" => $faker->firstName,
        "last_name" => $faker->lastName
    ];
});
