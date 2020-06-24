<?php

use Illuminate\Database\Seeder;
use App\Modes\User;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user1 = User::create([
            "name" => "Андрей Сухенко",
            "email" => "andreysuha2@gmail.com",
            "password" => Hash::make("123123123")
        ]);

        $user2 =  User::create([
            "name" => "Владимир Захарченко",
            "email" => "web.jungle.ua@gmail.com",
            "password" => Hash::make("123123123")
        ]);

        $user1->markEmailAsVerified();
        $user2->markEmailAsVerified();
    }
}
