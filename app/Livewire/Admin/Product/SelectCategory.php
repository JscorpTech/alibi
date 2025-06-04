<?php

namespace App\Livewire\Admin\Product;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;

class SelectCategory extends Component
{
    public array|object $categories = [];
    public array|object $subcategories = [];
    public mixed $offers;
    public mixed $old_categories;
    public mixed $old_subcategories;

    public function mount(): void
    {
        $this->categories = Category::query()->get();
        $this->subcategories = SubCategory::query()->get();
    }

    public function render(): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.admin.product.select-category');
    }
}
