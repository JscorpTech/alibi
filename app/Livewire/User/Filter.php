<?php

namespace App\Livewire\User;

use App\Models\Size;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;

class Filter extends Component
{
    public function render(): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        $sizes = Size::query()->orderByDesc('id')->get();

        return view('livewire.user.filter', [
            'sizes' => $sizes,
        ]);
    }
}
