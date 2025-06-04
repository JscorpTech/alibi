<?php

namespace Database\Seeders;

use App\Models\Size;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sizes = [
            'sm',
            'md',
            'xl',
            'lg',
            'xxl',
        ];

        foreach ($sizes as $size) {
            Size::query()->create([
                'name' => $size,
            ]);
        }
    }
}
