<?php

use Illuminate\Database\Seeder;

class ProductsGrigsTableSeeder extends Seeder
{
    private $images = [
        "public/images/company/1200px-Apple_logo_grey.svg.png",
        "public/images/company/200px-Xiaomi_logo.svg.png",
        "public/images/company/1200px-Huawei_Standard_logo.svg.png",
        "public/images/company/HP_New_Logo_2D.svg",
        "public/images/company/Image_jagran_english_21585486801447.jpg",
        "public/images/company/Nokia-Logo.jpg",
        "public/images/company/oneplus-logo-before-march-2020.jpg",
        "public/images/company/samsung-logo-1993.jpg.jpeg"
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Models\ProductGrid::class, 150)->create()->each(function ($productGrid) {
            if($productGrid->type === "carrier") {
                $productGrid->attach($this->images[rand(0, 7)], [ "key" => "thumbnail" ]);
            }
        });
    }
}
