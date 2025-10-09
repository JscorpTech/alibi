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
    /**
     * Создание товара (без какого-либо inventory).
     */
    public function store($request)
    {
        // 📸 главное изображение (может быть пустым)
        $image = $request->hasFile('image')
            ? Storage::putFile('products/', $request->file('image'))
            : null;

        // 🧠 статус публикации
        $isActive = !empty($image);
        $channel  = $isActive ? 'online' : 'warehouse';

        // 💰 цены
        $price     = str_replace(' ', '', (string) $request->input('price'));
        $discount  = $request->filled('discount')
            ? str_replace(' ', '', (string) $request->input('discount'))
            : null;
        $costPrice = (float) ($request->input('cost_price') ?? 0);

        // если нет цены — берём наценку от себестоимости (опционально)
        if (empty($price) && $costPrice > 0) {
            $price = round($costPrice * 1.3, 2);
        }

        // ✅ создаём товар
        $product = Product::query()->create([
            ...$request->only([
                ...LocaleService::getLocaleFields('name'),
                ...LocaleService::getLocaleFields('desc'),
                'category_id',
                'gender',
                'status',
            ]),
            'image'      => $image,
            'discount'   => $discount,
            'price'      => $price,
            // Храним counts как JSON-строку для совместимости со старым UI
            'count'      => json_encode($request->input('counts')),
            'cost_price' => $costPrice,
            'is_active'  => $isActive,
            'channel'    => $channel,
        ]);

        // 🔗 связи (размеры/цвета)
        if ($request->filled('sizes')) {
            $product->sizes()->attach($request->input('sizes'));
        }
        if ($request->filled('colors')) {
            $product->colors()->attach($request->input('colors'));
        }

        // 🖼️ доп. галерея (опционально)
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imageFile) {
                $path  = Storage::putFile('products/', $imageFile);
                $media = new Media(['path' => $path]);
                $product->images()->save($media);
            }
        }

        return $product;
    }

    /**
     * Просмотр товара (без inventory).
     */
    public function show($id): array
    {
        $product = Product::findOrField($id);
        $likes   = Like::query()->where(['product_id' => $product->id])->count();

        $items = [
            ['label' => __('product.name'),     'value' => $product->name],
            ['label' => __('product.count'),    'value' => $product->options()->sum('count')],
            [
                'label' => __('product.bought'),
                'value' => Order::query()
                    ->where(['status' => OrderStatusEnum::SUCCESS, 'product_id' => $product->id])
                    ->count() . ' ' . __('ta'),
            ],
            ['label' => __('product.sizes'),    'value' => implode(' | ', array_column($product->sizes->toArray(), 'name'))],
            ['label' => __('product.likes'),    'value' => $likes . ' ' . __('ta')],
            ['label' => __('category'),         'value' => $product->categoryNames()],
            ['label' => __('subcategory'),      'value' => $product->subCategoryNames()],
            ['label' => __('gender'),           'value' => __($product->gender)],
            ['label' => __('product.price'),    'value' => number_format($product->price) . " so'm"],
            ['label' => __('product.discount'), 'value' => number_format($product->discount) . " so'm"],
            ['label' => __('product.status'),   'value' => __($product->status)],
        ];

        return compact('product', 'likes', 'items');
    }

    /**
     * Обновление товара (без inventory).
     */
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
            'discount'   => $request->filled('discount')
                ? str_replace(' ', '', (string) $request->input('discount'))
                : null,
            'count'      => json_encode($request->input('counts')),
            'price'      => str_replace(' ', '', (string) $request->input('price')),
            'cost_price' => $request->input('cost_price') ?? $product->cost_price,
        ];

        // 📸 если обновили фото — активируем онлайн; если фото нет совсем — только склад
        if ($request->hasFile('image')) {
            $path             = Storage::putFile('products/', $request->file('image'));
            $data['image']    = $path;
            $data['is_active']= true;
            $data['channel']  = 'online';
        } elseif (empty($product->image)) {
            $data['is_active']= false;
            $data['channel']  = 'warehouse';
        }

        $product->fill($data)->save();

        // 🔗 связи
        $product->sizes()->sync($request->input('sizes', []));
        $product->colors()->sync($request->input('colors', []));

        // 🖼️ доп. фото
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imageFile) {
                $path  = Storage::putFile('products/', $imageFile);
                $media = new Media(['path' => $path]);
                $product->images()->save($media);
            }
        }

        return $product;
    }

    /**
     * Список товаров (без inventory).
     */
    public function index($request): array
    {
        $products = CacheService::remember(function () use ($request) {
            $data = [];

            if ($request->has('status') && $request->get('status') !== 'all') {
                $data['status'] = $request->get('status');
            }

            return Product::query()
                ->where($data)
                ->orderByDesc('id')
                ->paginate(21);
        }, $request);

        $allProducts = CacheService::remember(function () {
            return Product::query();
        }, $request);

        return compact('products', 'allProducts');
    }
}