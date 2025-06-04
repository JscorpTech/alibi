<?php

namespace Database\Seeders;

use App\Enums\GenderEnum;
use App\Models\Color;
use App\Models\Media;
use App\Models\Product;
use App\Models\ProductColors;
use App\Models\Size;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::factory(200)->create();

        $products = [
            [
//                "name_uz" => "Shim",
                'name_ru' => 'Shim',
//                "desc_uz" => "Shim desc",
                'desc_ru'  => 'Shim desc',
                'gender'   => GenderEnum::MALE,
                'price'    => 1000,
                'discount' => 900,
            ],
        ];
        foreach ($products as $product) {
            $product = Product::query()->create([
                'name_ru' => $product['name_ru'],
                'desc_ru' => $product['desc_ru'],
                'image'   => 'products/main.jpg',

                'gender'   => $product['gender'],
                'price'    => $product['price'],
                'discount' => $product['discount'],
            ]);
            ProductColors::create([
                'product_id' => $product->id,
                'color_id'   => Color::query()->find(1)->id,
            ]);

            $product->sizes()->attach(Size::query()->find(1));
            $images = [
                'pink.jpg',
                'main.jpg',
                'white.png',
                'what.png',
            ];

            foreach (range(1, 201) as $i) {
                Product::findOrField($i)->images()->create([
                    'path' => 'products/' . $images[array_rand($images)],
                    'name' => 'Test image',
                ]);
            }
        }
    }
}
