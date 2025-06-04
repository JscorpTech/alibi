<?php

namespace App\Services;

use App\Enums\BannerEnum;
use App\Enums\BannerStatusEnum;
use App\Enums\ProductStatusEnum;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Models\SubCategory;
use Exception;
use Illuminate\Support\Facades\Request;

class HomeService
{
    /**
     * get products and banners
     *
     * @return array
     */
    public function index(): array
    {
        return CacheService::remember(function () {
            $products = Product::query()->where(['status' => ProductStatusEnum::AVAILABLE])->orderByDesc('id')->limit(50)->get();
            $banners = [
                'top'    => Banner::query()->where(['position' => BannerEnum::TOP, 'status' => BannerStatusEnum::ACTIVE])->get(),
                'bottom' => Banner::query()->where(['position' => BannerEnum::BOTTOM, 'status' => BannerStatusEnum::ACTIVE])->get(),
            ];

            return [
                'products' => $products,
                'banners'  => $banners,
            ];
        }, key: md5('home'));
    }

    /**
     * Show product
     *
     * @param $id
     * @return array
     * @throws Exception
     */
    public function show($id): array
    {
        try {
            $product = Product::findOrField($id);
        } catch (\Throwable $e) {
            abort(404);
        }
        $codes = [];
        foreach ($product->subcategories as $subcategory) {
            $codes[] = $subcategory->code;
        }
        if (($key = array_search(1, $codes)) !== false) {
            unset($codes[$key]);
        }

        $products = Product::query()->where(['status' => ProductStatusEnum::AVAILABLE])->whereHas('subcategories', function ($query) use ($codes) {
            $query->whereIn('code', $codes);
        })->limit(15)->get();

        return [
            'product'  => $product,
            'products' => $products,
            'size'     => Request::get('size'),
        ];
    }

    /**
     * Get all categories list
     *
     * @return mixed
     */
    public function categories(): mixed
    {
        return CacheService::remember(function () {
            return Category::query()->get();
        }, key: md5('categories'));
    }
}
