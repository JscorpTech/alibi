<?php

namespace App\Livewire\User\Show;

use App\Models\SubCategory;
use App\Services\ProductService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;

class Categories extends Component
{
    public mixed $product;
    public mixed $categories;
    public mixed $products;
    public mixed $now_category;

    protected $listeners = ['setCategory'];

    public function setCategory($id): void
    {
        $this->now_category = SubCategory::findOrField($id);
        $this->getProducts();
    }

    public function getProducts()
    {
        $this->products = $this->now_category->products()->limit(15)->get();
    }

    public function mount($product): void
    {
        $service = new ProductService();
        $categories = $service->getOffers($product);
        $this->categories = $categories;
        $this->setCategory($this->categories->first()->id);
    }

    public function render(): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.user.show.categories');
    }
}
