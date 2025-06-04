<?php

namespace App\Livewire\Admin;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;

class SubCategoryDelete extends Component
{
    /**
     * Delete SubCategory
     *
     * @param $id
     * @return void
     * @throws \Exception
     */
    public function delete($id): void
    {
        \App\Models\SubCategory::findOrField($id)->delete();
        $this->dispatch('closeModal');
        $this->dispatch('getSubCategories');
    }

    public function render(): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.admin.sub-category-delete');
    }
}
