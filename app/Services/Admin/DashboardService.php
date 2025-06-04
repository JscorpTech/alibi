<?php

namespace App\Services\Admin;

use App\Enums\OrderStatusEnum;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\CacheService;
use Carbon\Carbon;

class DashboardService
{
    public function index($request): array
    {
        $data = CacheService::remember(function () {
            $monthOrders = Order::query()
                ->where('created_at', '>=', Carbon::now()->subMonth())
                ->where('status', OrderStatusEnum::SUCCESS);

            return [
                'monthOrders'      => $monthOrders,
                'monthOrdersPrice' => $monthOrders->sum('price'),
                'ordersPrice'      => Order::query()->where('status', OrderStatusEnum::SUCCESS)->sum('price'),
                'categories'       => $this->getCategories($monthOrders),
                'users'            => User::query()->count(),
                'cCount'           => Category::query()->count(),
                'products'         => Product::query()->count(),
            ];
        }, $request);
        $info = [
            [
                'name'  => __('total:orders'),
                'value' => Order::query()->count(),
                'color' => '#009DFFFF',
            ],
            [
                'name'  => __('success:orders'),
                'value' => Order::query()->where(['status' => OrderStatusEnum::SUCCESS])->count(),
                'color' => 'var(--falcon-green)',
            ],
            [
                'name'  => __('pending:orders'),
                'value' => Order::query()->where(['status' => OrderStatusEnum::PENDING])->count(),
                'color' => 'orange',
            ],
            [
                'name'  => __('canceled:orders'),
                'value' => Order::query()->where(['status' => OrderStatusEnum::CANCELED])->count(),
                'color' => 'var(--falcon-red)',
            ],
        ];

        return [
            'monthOrdersPrice' => $data['monthOrdersPrice'],
            'ordersPrice'      => $data['ordersPrice'],
            'categories'       => $data['categories'],
            'monthOrders'      => $data['monthOrders'],
            'info'             => $info,
            'users'            => $data['users'],
            'cCount'           => $data['cCount'],
            'products'         => $data['products'],
        ];
    }

    public function getCategories($monthOrders): array
    {
        $categories = [];
        foreach ($monthOrders->get() as $item) {
            $c = $item->product->categories;
            foreach ($c as $i) {
                $id = $i->id;
                if (empty($categories[$id])) {
                    $categories[$id] = 1;
                } else {
                    $categories[$id] += 1;
                }
            }
        }

        $res = [];
        $sum = $monthOrders->count();
        foreach ($categories as $key => $value) {
            $category = Category::findOrField($key);
            $res[] = [
                'name'  => $category->name,
                'value' => round($value / $sum * 100, 2),
            ];
        }
        $values = array_column($res, 'value');
        array_multisort($values, SORT_DESC, $res);

        return $res;
    }
}
