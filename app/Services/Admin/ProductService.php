<?php

namespace App\Services\Admin;

use App\Enums\OrderStatusEnum;
use App\Models\Like;
use App\Models\Media;
use App\Models\Order;
use App\Models\Product;
use App\Services\CacheService;
use App\Services\LocaleService;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    public function store($request)
    {
        $image = Storage::putFile('products/', $request->file('image'));
        $product = Product::query()->create(
            [
                ...$request->only([
                    ...LocaleService::getLocaleFields('name'),
                    ...LocaleService::getLocaleFields('desc'),
                    'category_id',
                    'gender',
                    'status',
                ]),
                'image'    => $image,
                'discount' => $request->input('discount') != null ? str_replace(' ', '', $request->input('discount')) : null,
                'price'    => str_replace(' ', '', $request->input('price')),
                'count'    => json_encode($request->input('counts')),
            ]
        );

        foreach ($request->input('sizes') as $size) {
            $product->sizes()->attach($size);
        }

        foreach ($request->input('colors') as $color) {
            $product->colors()->attach($color);
        }

        foreach ($request->file('images') as $image) {
            $file = Storage::putFile('products/', $image);
            $media = new Media([
                'path' => $file,
            ]);
            $product->images()->save($media);
        }

        return $product;
    }

    public function show($id): array
    {
        $product = Product::findOrField($id);
        $likes = Like::query()->where(['product_id' => $product->id])->count();

        $items = [
            [
                'label' => __('product.name'),
                'value' => $product->name,
            ],
            [
                'label' => __('product.count'),
                'value' => $product->options()->sum('count'),
            ],
            [
                'label' => __('product.bought'),
                'value' => Order::query()->where(['status' => OrderStatusEnum::SUCCESS, 'product_id' => $product->id])->count() . ' ' . __('ta'),
            ],
            [
                'label' => __('product.sizes'),
                'value' => implode(' | ', array_column($product->sizes->toArray(), 'name')),
            ],
            [
                'label' => __('product.likes'),
                'value' => $likes . ' ' . __('ta'),
            ],
            [
                'label' => __('category'),
                'value' => $product->categoryNames(),
            ],
            [
                'label' => __('subcategory'),
                'value' => $product->subCategoryNames(),
            ],
            [
                'label' => __('gender'),
                'value' => __($product->gender),
            ],
            [
                'label' => __('product.price'),
                'value' => number_format($product->price) . " so'm",
            ],
            [
                'label' => __('product.discount'),
                'value' => number_format($product->discount) . " so'm",
            ],
            [
                'label' => __('product.status'),
                'value' => __($product->status),
            ],
        ];

        return [
            'product' => $product,
            'likes'   => $likes,
            'items'   => $items,
        ];
    }

    public function update($id, $request)
    {
        $product = Product::findOrField($id);
        $data = [
            ...$request->only([
                ...LocaleService::getLocaleFields('name'),
                ...LocaleService::getLocaleFields('desc'),
                'category_id',
                'gender',
                'status',
            ]),
            'discount' => $request->input('discount') != null ? str_replace(' ', '', $request->input('discount')) : null,
            'count'    => json_encode($request->input('counts')),
            'price'    => str_replace(' ', '', $request->input('price')),
        ];

        if ($request->hasFile('image')) {
            $path = Storage::putFile('products/', $request->file('image'));
            $data['image'] = $path;
        }

        $product->fill($data);
        $product->save();

        $product->sizes()->detach();
        $product->colors()->detach();

        foreach ($request->input('sizes') as $item) {
            $product->sizes()->attach($item);
        }

        foreach ($request->input('colors') as $item) {
            $product->colors()->attach($item);
        }

        return $product;
    }

    public function index($request): array
    {
        $products = CacheService::remember(function () use ($request) {
            $data = [];
            if ($request->has('status')) {
                $status = $request->get('status');
                if ($status != 'all') {
                    $data['status'] = $status;
                }
            }

            return Product::query()
                ->where($data)
                ->orderByDesc('id')
                ->paginate(21);
        }, $request);

        $allProducts = CacheService::remember(function () {
            return Product::query();
        }, $request);

        return [
            'products'    => $products,
            'allProducts' => $allProducts,
        ];
    }
}
