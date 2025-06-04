<?php

namespace App\Livewire\User;

use App\Enums\ProductStatusEnum;
use App\Models\Product;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;
use Livewire\WithPagination;

class Products extends Component
{
    use WithPagination;

    public array $filter = []; // Filters
    public string $sort = 'id'; // Filter sort field
    public string $direction = 'desc'; // Filter direction Enum: asc|desc
    public array $sizes = []; // Product selected sizes
    public string|null $type; // Action type Enum: create|update
    public string|null $id; // Product id

    protected $listeners = ['setFilter' => 'setFilter']; // Listeners

    protected $queryString = [
        'sort' => ['except' => 'id'],
        'direction' => ['except' => 'desc'],
        'sizes' => ['except' => []],
        'type' => ['except' => null],
        'id' => ['except' => null],
        'page' => ['except' => 1],
    ];

    /**
     * Update filter
     * @param string $sort
     * @param string $direction
     * @param array $sizes
     * @return void
     */
    public function setFilter(string $sort = 'id', string $direction = 'desc', array $sizes = []): void
    {
        $this->sort = $sort;
        $this->sizes = $sizes;
        $this->direction = $direction;
        $this->resetPage();
    }

    public function render(): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        $products = Product::query()->where(['status' => ProductStatusEnum::AVAILABLE])->orderBy($this->sort, $this->direction);

        if ($this->type == 'subcategory') {
            $products = $products->whereHas('subcategories', function ($query) {
                $query->where('sub_category_id', $this->id);
            });
        } else {
            $products = $products->whereHas('categories', function ($query) {
                $query->where('category_id', $this->id);
            });
        }

        if ($this->sizes) {
            $products = $products->whereHas('sizes', function ($query) {
                $query->whereIn('size_id', $this->sizes);
            });
        }

        $products = $products->paginate(30);

        return view('livewire.user.products', [
            'products' => $products,
        ]);
    }
}
