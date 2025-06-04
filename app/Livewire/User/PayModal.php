<?php

namespace App\Livewire\User;

use App\Enums\DiscountEnum;
use App\Enums\PaymentTypeEnum;
use App\Models\Address;
use App\Models\District;
use App\Models\OrderGroup;
use App\Models\Product;
use App\Models\Region;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Cookie;
use Livewire\Component;

class PayModal extends Component
{
    public mixed $product; // Product

    /**
     * Address
     * @var int
     */
    public int $region;
    public int $district;

    public mixed $regions = [];
    public mixed $districts = [];

    protected $listeners = ['getDistricts' => 'getDistricts'];

    /**
     * PayModal validate rules
     *
     * @return array
     */
    public function getRules(): array
    {
        return [
            'region'   => ['required', 'exists:regions,id'],
            'district' => ['required', 'exists:districts,id'],
        ];
    }

    /**
     * Get districts after region selection
     *
     * @return void
     */
    public function getDistricts(): void
    {
        $this->districts = District::query()->where(['region_id' => $this->region])->get();
    }

    /**
     * Component mounted event
     *
     * @return void
     */
    public function mount(): void
    {
        $this->regions = Region::query()->get();
    }

    /**
     * Create Order
     * @param $product
     * @param $color
     * @param $size
     * @param $payment_type // Not use
     * @param $count
     * @param $address
     * @return void
     * @throws Exception
     */
    public function order($product, $color, $size, $payment_type, $count, $address): void
    {
        $this->validate();

        $user = \Illuminate\Support\Facades\Auth::user();
        $product = Product::findOrField($product);
        $address = Address::query()->create([
            'label'       => $address,
            'region_id'   => $this->region,
            'district_id' => $this->district,
        ]);

        $price = ($product->getPriceNumber() * $count);

        $discount = 0;

        if ($user->is_first_order) {
            $discount = (int) (($product->price * $count) * (DiscountEnum::FIRST_ORDER / 100));
            $user->is_first_order = false;
            $user->save();
        }

        $order_group = OrderGroup::query()->create([
            'user_id'      => \Illuminate\Support\Facades\Auth::id(),
            'address_id'   => $address->id,
            'payment_type' => PaymentTypeEnum::CASH,
        ]);
        \App\Models\Order::query()->create([
            'product_id'     => $product->id,
            'color_id'       => $color,
            'size_id'        => $size,
            'count'          => $count,
            'price'          => $price,
            'discount'       => $discount,
            'order_group_id' => $order_group->id,
        ]); // Insert Order model

        Cookie::make('is_first_order', true);

        $this->dispatch('success'); // close modal event
    }

    public function render(): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.user.pay-modal');
    }
}
