<?php

namespace Database\Seeders;

use App\Models\Color;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $colors = [
            (object) ['name' => 'Oq','color' => '#fff'],
            (object) ['name' => 'Qora','color' => '#000'],
            (object) ['name' => 'Qizil','color' => '#e63757'],
            (object) ['name' => 'Yashil','color' => '#00d27a'],
            (object) ['name' => "Ko'k",'color' => '#2c7be5'],
        ];

        foreach ($colors as $color) {
            Color::query()->create([
                'name'  => $color->name,
                'color' => $color->color,
            ]);
        }
    }
}
