<?php

namespace App\View\Components;

use App\Enums\OrderStatusEnum;
use App\Models\Order;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Navbar extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $ordersCount = Order::query()->where(['status' => OrderStatusEnum::PENDING])->count();

        return view('components.navbar', [
            'ordersCount' => $ordersCount,
        ]);
    }
}
