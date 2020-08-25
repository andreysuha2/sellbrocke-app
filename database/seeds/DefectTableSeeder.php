<?php

use Illuminate\Database\Seeder;

class DefectTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Models\Defect::class, 100)->create();
    }
}
