<?php

use Illuminate\Database\Seeder;
use App\Models\Company;

class CompanyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $companies = [
            [
                "name" => "Apple",
                "price_reduction" => 20,
                "slug" => "apple",
                "image" => "public/images/company/1200px-Apple_logo_grey.svg.png"
            ],
            [
                "name" => "Xiaomi",
                "price_reduction" => 25,
                "slug" => "xiaomi",
                "image" => "public/images/company/200px-Xiaomi_logo.svg.png"
            ],
            [
                "name" => "Huawei",
                "price_reduction" => 23,
                "slug" => "huawei",
                "image" => "public/images/company/1200px-Huawei_Standard_logo.svg.png"
            ],
            [
                "name" => "HP",
                "price_reduction" => 12,
                "slug" => "hp",
                "image" => "public/images/company/HP_New_Logo_2D.svg"
            ],
            [
                "name" => "Oppo",
                "price_reduction" => 15,
                "slug" => "oppo",
                "image" => "public/images/company/Image_jagran_english_21585486801447.jpg"
            ],
            [
                "name" => "Nokia",
                "price_reduction" => 30,
                "slug" => "nokia",
                "image" => "public/images/company/Nokia-Logo.jpg"
            ],
            [
                "name" => "One plus",
                "price_reduction" => 26,
                "slug" => "one-plus",
                "image" => "public/images/company/oneplus-logo-before-march-2020.jpg"
            ],
            [
                "name" => "Samsung",
                "price_reduction" => 26,
                "slug" => "samsung",
                "image" => "public/images/company/samsung-logo-1993.jpg.jpeg"
            ]
        ];
        foreach ($companies as $companyData) {
            $image = $companyData["image"];
            unset($companyData["image"]);
            $company = Company::create($companyData);
            $company->attach($image, [ "key" => "logo" ]);
        }
    }
}
