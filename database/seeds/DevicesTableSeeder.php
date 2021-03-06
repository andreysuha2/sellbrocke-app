<?php

use Illuminate\Database\Seeder;

class DevicesTableSeeder extends Seeder
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

    private $categoriesIds;
    private $productsGridsIds = [ "carriers" => null, "sizes" => null ];
    private $faker;

    public function __construct() {
        $this->categoriesIds = \App\Models\Category::whereIsLeaf()
            ->get()
            ->map(function ($category) { return $category->id; });
        $this->productsGridsIds["carriers"] = \App\Models\ProductGrid::where("type", "carrier")
            ->get()
            ->map(function ($productGrid) { return $productGrid->id; });
        $this->productsGridsIds["sizes"] = \App\Models\ProductGrid::where("type", "size")
            ->get()
            ->map(function ($productGrid) { return $productGrid->id; });
        $this->faker = \Faker\Factory::create();
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Models\Device::class, 100)->create()->each(function ($device) {
            $device->attach($this->images[rand(0, 7)], [ "key" => "thumbnail" ]);
            $device->categories()->attach($this->faker->randomElements($this->categoriesIds, rand(1, 5)));
            if($device->use_products_grids) {
                $device->productsGrids()->attach($this->faker->randomElements($this->productsGridsIds["carriers"], rand(1, 5)));
                $device->productsGrids()->attach($this->faker->randomElements($this->productsGridsIds["sizes"], rand(1, 5)));
            }
        });
    }
}
