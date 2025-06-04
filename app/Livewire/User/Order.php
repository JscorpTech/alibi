<?php

namespace App\Livewire\User;

use App\Models\ProductOption;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use JetBrains\PhpStorm\NoReturn;
use Livewire\Component;

class Order extends Component
{
    public mixed $product; // Product
    public bool $is_basket = false; // Product is basket

    public int|null $color = null; // Selected color
    public int|null $size = null; // Selected size

    protected $listeners = [
        'size'  => 'size',
        'color' => 'color',
    ]; // Listeners

    public array $already = [
        'color' => [],
        'size'  => [],
    ]; // Product color or size already info

    /**
     * Product Add Wishlist
     *
     * @return void
     * @author Azamov Samandar
     */
    public function addWishlist(): void
    {
        /**
         * @var $user User
         */
        $user = \Illuminate\Support\Facades\Auth::user();

        /**
         * Product if basket detach product
         */
        if ($this->isBasket()) {
            $user->likes()->detach($this->product->id);
            $this->dispatch('basket_remove');
            $this->is_basket = false;

            return;
        }

        /**
         * If Product basket not found add basket
         */
        $this->is_basket = true;
        $user->likes()->attach($this->product->id);
        $this->dispatch('basket_add');
    }

    /**
     * Product is basket in
     * @return bool
     */
    public function isBasket(): bool
    {
        /**
         * @var $user User
         */
        $user = \Illuminate\Support\Facades\Auth::user();
        if (!$user) {
            return false;
        }
        if ($user->likes()->where(['product_id' => $this->product->id])->exists()) {
            return true;
        }

        return false;
    }

    /**
     * Select color
     * @return void
     */
    public function submit(): void
    {
        $data = [
            'product_id' => $this->product->id,
        ];
        if ($this->color != null) {
            $data['color_id'] = $this->color;
        }

        $option = ProductOption::query()->where($data)->where('count', '>=', 1)->get();
        $this->already['size'] = array_column($option->toArray(), 'size_id');
    }

    #[NoReturn]
    public function color($value): void
    {
        $this->color = $value;
        $this->submit();
    }

    #[NoReturn]
    public function size($value): void
    {
        $this->size = $value;
    }

    public function mount(): void
    {
        $this->color($this->product->colors()->first()?->color->id);
        if ($this->isBasket()) {
            $this->is_basket = true;
        } else {
            $this->is_basket = false;
        }
    }

    public function render(): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.user.order');
    }
}
