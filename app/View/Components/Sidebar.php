<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;
use Illuminate\View\Component;

class Sidebar extends Component
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
        $dropdowns = [
            [
                'name'  => 'product',
                'icon'  => 'fa-shopping-basket',
                'items' => [
                    'index'  => route('product.index'),
                    'create' => route('product.create'),
                ],
            ],
            [
                'name'  => 'category',
                'icon'  => 'fa-list-alt',
                'items' => [
                    'index'       => route('category.index'),
                    'create'      => route('category.create'),
                    'subcategory' => route('subcategory'),
                ],
            ],
            [
                'name'  => 'settings',
                'icon'  => 'fa-list-alt',
                'items' => [
                    'size'     => route('size.index'),
                    'color'    => route('color.index'),
                    'banners'  => route('banners'),
                    'sizes'    => route('size.info'),
                    'settings' => route('settings.index'),
                ],
            ],

        ];
        $menus = [
            'dashboard' => [
                'route' => route('dashboard'),
                'icon'  => 'fa-chart-bar',
            ],
            __('Admin v2') => [
                'route' => '/admin-v2',
                'icon'  => 'fa-chart-bar',
            ],

            'order.index' => [
                'route' => route('order.index'),
                'icon'  => 'fa-edit',
            ],
            'user.index' => [
                'route' => route('user.index'),
                'icon'  => 'fa-user',
            ],

        ];

        $route = explode('.', Request::route()->getName());
        if (count($route) == 1) {
            $route[] = '';
        }

        return view('components.sidebar', [
            'dropdowns'  => $dropdowns,
            'menus'      => $menus,
            'route'      => $route,
            'route_name' => Request::route()->getName(),
        ]);
    }
}
