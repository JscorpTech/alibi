<?php

namespace App\Services\Admin;

use App\Enums\OrderStatusEnum;
use App\Http\Helpers\Helper;
use App\Models\Category;
use App\Models\OrderGroup;
use App\Models\ProductOption;
use App\Services\CacheService;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Log;

class OrderService
{
    public static function first_order_sync($user): void
    {
        $orders = OrderGroup::query()
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->where('status', OrderStatusEnum::SUCCESS);
            })
            ->orWhere(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->where('status', OrderStatusEnum::PENDING);
            })
            ->count();

        if ($orders >= 1) {
            $user->is_first_order = false;
        } else {
            $user->is_first_order = true;
        }
        $user->save();
    }

    public function filter($filter): array
    {
        return CacheService::remember(function () use ($filter) {
            $data = [];
            if ($filter->has('status')) {
                $status = $filter->input('status');
                if ($status != 'all') {
                    $data['status'] = $status;
                }
            }
            $orders = OrderGroup::query();
            if ($filter->has('phone')) {
                $phone = Helper::clearPhone($filter->get('phone'));
                if (preg_match('/^[0-9]{12}/', $phone)) {
                    $orders->whereHas('user', function ($query) use ($phone) {
                        $query->where(['phone' => $phone]);
                    });
                }
            }
            if ($filter->has('category_id')) {
                $category = $filter->get('category_id');
                if ($category != 'all') {
                    $orders = OrderGroup::whereHas('orders', function ($query) use ($category) {
                        $query->whereHas('product', function ($query) use ($category) {
                            $query->whereHas('categories', function ($subQuery) use ($category) {
                                $subQuery->where('categories.id', $category);
                            });
                        });
                    });
                }
            }
            //            with("product", function ($query) {
            //                $query->withTrashed();
            //            })
            $orders = $orders->where('user_id', '!=', '36')
                ->where('user_id', '!=', '1')
                ->where($data)->orderByDesc('id')
                ->paginate(Env::get('PAGE_SIZE'))
                ->appends($filter->all());
            $categories = Category::query()->get();

            return [
                'orders' => $orders,
                'categories' => $categories,
            ];
        }, $filter);
    }

    public function delete($id): bool
    {
        try {
            $order = OrderGroup::findOrField($id);
            $order->delete();

            return true;
        } catch (\Throwable $e) {
            Log::error($e->getMessage());

            return false;
        }
    }

    public static function editProductOption($orderGroup, $action = 'add'): void
    {
        foreach ($orderGroup->orders as $order) {
            // если чего-то нет — пропускаем позицию
            if (empty($order->product_id) || empty($order->color_id) || empty($order->size_id)) {
                continue;
            }

            $optionQ = \App\Models\ProductOption::query()->where([
                'product_id' => $order->product_id,
                'color_id' => $order->color_id,
                'size_id' => $order->size_id,
            ]);

            if (!$optionQ->exists()) {
                continue;
            }

            $current = (int) ($optionQ->first()->count ?? 0);

            if ($action === 'remove') {
                $optionQ->update(['count' => $current - (int) $order->count]);
            } else {
                $optionQ->update(['count' => $current + (int) $order->count]);
            }
        }
    }
}
