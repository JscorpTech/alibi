<?php

namespace App\Services;

use App\Http\Controllers\BaseController;
use App\Http\Helpers\ExceptionHelper;
use App\Http\Resources\Api\Product\ProductListResource;
use App\Models\Category;
use App\Models\Product;
use App\Models\SubCategory;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class ProductService
{
    use BaseController;

    /**
     * Get products
     *
     * @param $filter
     * @return array|JsonResponse
     */
    public function index($filter): array|JsonResponse
    {
        return CacheService::remember(function () use ($filter) {
            $products = Product::query();

            if(!$filter->has("sort")){
                $products = $products->inRandomOrder();
            }

            $category_id = $filter->input('category_id');
            if ($category_id) {
                $products = Product::query()->orderByDesc(Category::query()->where(['id'=>$category_id])->first()->sortby);
                $products = $products->whereHas('categories', function ($query) use ($category_id) {
                    $query->where('categories.id', $category_id);
                });
            }

            $category_id = $filter->input('subcategory_id');
         
            if ($category_id) {
                $products = Product::query()->orderByDesc(SubCategory::query()->where(['id'=>$category_id])->first()->category->sortby);
                $products = $products->whereHas('subcategories', function ($query) use ($category_id) {
                    $query->where('sub_category_id', $category_id);
                });
            }
            if ($filter->has('brand')) {
                $products = $products->where('brand_id', $filter->input('brand'));
            }
            if ($filter->has('price')) {
                $price = explode(',', $filter->input('price'));
                $from = $price[0];
                $to = $price[1];

                if ($from > $to) {
                    ExceptionHelper::sendError(__('price.filter:error'));
                }

                $products = $products
                    ->where('price', '>=', $from)
                    ->where('price', '<=', $to)
                    ->orWhere(function ($query) use ($from, $to) {
                        $query->where('discount', '>=', $from);
                        $query->where('discount', '<=', $to);
                    });
            }

            if ($filter->input('search')) {
                $products = $products
                    ->where(
                        DB::raw('LOWER(name_' . App::getLocale() . ')'),
                        'like',
                        '%' . strtolower($filter->input('search')) . '%'
                    )
                    ->orWhere(
                        DB::raw('LOWER(desc_' . App::getLocale() . ')'),
                        'like',
                        '%' . strtolower($filter->input('search')) . '%'
                    );
            }
            if ($filter->input('sort')) {
                $field = $filter->input('sort');
                $by = $filter->input('sort_by', 'asc');
                if ($by == 'desc') {
                    $products = $products->orderBy($field);
                } else {
                    $products = $products->orderByDesc($field);
                }
            } else {
                $products = $products->orderByDesc('id');
            }
            $products = $products->paginate(Env::get('PAGE_SIZE', 10));

            return ProductListResource::paginate($products);
        }, $filter);
    }

    /**
     * Get product offers
     *
     * @param $product
     * @return SubCategory|Builder[]|Collection|\Illuminate\Support\Collection
     */
    public function getOffers($product): Collection|SubCategory|\Illuminate\Support\Collection|array
    {
        try {
            $categories = SubCategory::query()->whereIn('id', $product->offers)->get();
            if ($categories->count() == 0) {
                try {
                    $categories = SubCategory::all()->random(2);
                } catch (\Throwable $e) {
                    return [];
                }
            }
        } catch (\Throwable $e) {
            $categories = SubCategory::all()->random(2);
        }

        return $categories;
    }
}
