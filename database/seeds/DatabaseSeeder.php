<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserTableSeeder::class);
        $this->call(MerchantTableSeeder::class);
        $this->call(DefectTableSeeder::class);
        $this->call(CompanyTableSeeder::class);
        $this->call(CategoriesTableSeeder::class);
        $this->call(ProductsGrigsTableSeeder::class);
        $this->call(DevicesTableSeeder::class);
        $this->call(ConditionTableSeeder::class);
        $this->call(CustomerTableSeeder::class);
    }
}
