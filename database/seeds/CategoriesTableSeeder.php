<?php

use Illuminate\Database\Seeder;
use App\Models\Category;
use Faker\Factory;

class CategoriesTableSeeder extends Seeder
{

    private $faker;
    private $defectsIds;

    public function __construct() {
        $this->faker = Factory::create();
        $this->defectsIds = range(1, 100);
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [
                "name" => "Laptop",
                "slug" => "laptop",
                "image" => "public/images/categories/Laptop.png"
            ],
            [
                "name" => "MacBook",
                "slug" => "macbook",
                "image" => "public/images/categories/MacBook.png",
                "children" => [
                    [
                        "name" => "MacBook",
                        "slug" => "macbook-base",
                        "image" => "public/images/categories/MacBook-90x90.png"
                    ],
                    [
                        "name" => "MacBook Air",
                        "slug" => "macbook-air",
                        "image" => "public/images/categories/MacBook_Air-90x90.png"
                    ],
                    [
                        "name" => "MacBook Pro",
                        "slug" => "macbook-pro",
                        "image" => "public/images/categories/MacBook_Pro-90x90.png"
                    ]
                ]
            ],
            [
                "name" => "iPhone",
                "slug" => "iphone",
                "image" => "public/images/categories/iPhoneIcon-90x90.png"
            ],
            [
                "name" => "iPad",
                "slug" => "ipad",
                "image" => "public/images/categories/ipadIcon-90x90.png",
                "children" => [
                    [
                        "name" => "iPad",
                        "slug" => "ipad-original",
                        "image" => "public/images/categories/sell-ipad-apple-tablet-90x90.png",
                        "children" => [
                            [
                                "name" => "iPad (1st generation)",
                                "slug" => "ipad-1st-generation",
                                "image" => "public/images/categories/Apple_iPad_1-88x90.png"
                            ],
                            [
                                "name" => "iPad (2nd generation)",
                                "slug" => "ipad-1nd-generation",
                                "image" => "public/images/categories/apple_ipad_2-100x90.png"
                            ],
                            [
                                "name" => "iPad (3rd generation)",
                                "slug" => "ipad-3rd-generation",
                                "image" => "public/images/categories/sell-ipad-3-apple-tablet-91x90.png"
                            ],
                            [
                                "name" => "iPad (4th generation)",
                                "slug" => "ipad-4th-generation",
                                "image" => "public/images/categories/sell-iPad-4-apple-tablet-109x90.png"
                            ],
                            [
                                "name" => "iPad (5th generation)",
                                "slug" => "ipad-5th-generation",
                                "image" => "public/images/categories/ipad_apple_5th_gen_tablet-96x90.png"
                            ],
                            [
                                "name" => "iPad (6th generation)",
                                "slug" => "ipad-6th-generation",
                                "image" => "public/images/categories/ipad-6th-generation-89x90.png"
                            ]
                        ]
                    ],
                    [
                        "name" => "iPad Air",
                        "slug" => "ipad-air",
                        "image" => "public/images/categories/sell-iPad-Air-Apple-tablet-110x90.png"
                    ],
                    [
                        "name" => "iPad Mini",
                        "slug" => "ipad-mini",
                        "image" => "public/images/categories/sell-iPad-Mini-3-tablet-81x90.png",
                    ],
                    [
                        "name" => "iPad Pro",
                        "slug" => "ipad-pro",
                        "image" => "public/images/categories/apple_ipad_pro-98x90.png"
                    ]
                ]
            ],
            [
                "name" => "Phones",
                "slug" => "phones",
                "image" => "public/images/categories/PhoneIcon-90x90.png"
            ]
        ];
        foreach ($categories as $category) $this->createCategory($category);
    }

    private function createCategory($data, $parent = null) {
        $image = $data["image"];
        $children = $data["children"] ?? null;
        $data["description"] = $this->faker->text(200);
        unset($data["image"]);
        unset($data["children"]);
        if($parent) {
            $data["slug"] = $parent->slug . "/" .  $data["slug"];
            $category = $parent->children()->create($data);
        } else $category = Category::create($data);
        $category->attach($image, [ "key" => "thumbnail" ]);
        $category->defects()->attach($this->faker->randomElements($this->defectsIds, rand(1, 10)));
        if($children) {
            foreach ($children as $child) $this->createCategory($child, $category);
        }
    }
}
