<?php

namespace App\View\Components\User\Com;

use App\Models\Category;
use App\Models\SubCategory;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Component;

class Header extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $categories = Category::query()->orderBy('position')->get();
        $current = Route::current();

        if ($current->parameter('type') == 'subcategory') {
            $current = SubCategory::findOrField($current->parameter('id'))->category->id;
        } else {
            $current = $current->parameter('id');
        }

        return view('components.user.com.header', [
            'categories' => $categories,
            'current'    => $current,
        ]);
    }
}
