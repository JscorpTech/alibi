<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use Livewire\Component;

class SubCategory extends Component
{
    public mixed $data;
    public mixed $categories = [];

    protected $listeners = ['getSubCategories' => 'getData'];

    public function getData(): void
    {
        $this->data = \App\Models\SubCategory::query()->orderByDesc('id')->get();
    }

    public function mount(): void
    {
        $this->categories = Category::query()->get();
        $this->getData();
    }

    public function render()
    {
        return view('livewire.admin.sub-category')->layout('components.layouts.main');
    }
}
