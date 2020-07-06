<?php

use Illuminate\Database\Seeder;
use App\Models\Merchant;
use Illuminate\Support\Facades\Hash;

class MerchantTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Merchant::create([
            "name" => "Sellbroke",
            "login" => "sellbroke",
            "password" => Hash::make("123123123"),
            "url" => env("DEFAULT_MERCHANT_SLUG", "http://localhost")
        ]);
    }
}
