<?php

use Illuminate\Database\Seeder;

class ConditionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Condition::create([
            "label" => "Like New",
            "description" => "Some description",
            "discount_percent" => 20
        ]);

        \App\Models\Condition::create([
            "label" => "Used (Good)",
            "description" => "Some description",
            "discount_percent" => 40
        ]);

        \App\Models\Condition::create([
            "label" => "Used (With Defects)",
            "description" => "Some description",
            "discount_percent" => 80
        ]);
    }
}
