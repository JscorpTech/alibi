<?php

namespace Database\Factories;

use App\Enums\GenderEnum;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $images = [
            'main.jpg',
            'white.png',

            '1.png',
            '2.jpg',
            '3.jpg',
            '4.jpg',
            '5.png',
        ];

        return [

            'name_ru' => fake()->name(),

            'desc_ru' => fake()->text(255),
            'image'   => 'products/' . $images[array_rand($images)],

            'gender'   => GenderEnum::MALE,
            'price'    => 1000,
            'discount' => 800,
        ];
    }
}
