<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'uz' => 'Shim',
                'ru' => 'Shim',
            ],
        ];

        foreach ($categories as $category) {
            Category::query()->create([
//                "name_uz" => $category['uz'],
                'name_ru' => $category['ru'],
//                "name_kr" => $category['ru'],
            ]);
        }
    }
}
