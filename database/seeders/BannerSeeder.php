<?php

namespace Database\Seeders;

use App\Enums\BannerEnum;
use App\Enums\BannerStatusEnum;
use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banners = [
            (object) ['title' => 'Test uchun banner', 'subtitle' => 'Test sub title', 'image' => 'products/main.jpg', 'link' => route('category', ['type' => 'category','id' => 1]), 'position' => BannerEnum::TOP, 'status' => BannerStatusEnum::ACTIVE],
            (object) ['title' => 'Test uchun banner', 'subtitle' => 'Test sub title', 'image' => 'products/main.jpg', 'link' => route('category', ['type' => 'category','id' => 1]), 'position' => BannerEnum::TOP, 'status' => BannerStatusEnum::ACTIVE],
            (object) ['title' => 'Test uchun banner', 'subtitle' => 'Test sub title', 'image' => 'products/main.jpg', 'link' => route('category', ['type' => 'category','id' => 1]), 'position' => BannerEnum::TOP, 'status' => BannerStatusEnum::ACTIVE],
            (object) ['title' => 'Test uchun banner', 'subtitle' => 'Test sub title', 'image' => 'products/main.jpg', 'link' => route('category', ['type' => 'category','id' => 1]), 'position' => BannerEnum::TOP, 'status' => BannerStatusEnum::ACTIVE],
        ];

        foreach ($banners as $banner) {
            Banner::query()->create([
                'title'    => $banner->title,
                'subtitle' => $banner->subtitle,
                'image'    => $banner->image,
                'link'     => $banner->link,
                'position' => $banner->position,
                'status'   => $banner->status,
            ]);
        }
    }
}
