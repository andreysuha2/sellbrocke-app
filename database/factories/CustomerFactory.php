<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Customer;
use Faker\Generator as Faker;

$factory->define(Customer::class, function (Faker $faker) {
    return [
        "merchant_id" => 1,
        "merchant_customer_id" => $faker->unique()->numberBetween(1, 100),
        "email" => $faker->email,
        "login" => $faker->name,
        "first_name" => $faker->firstName,
        "last_name" => $faker->lastName
    ];
});
