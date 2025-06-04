<?php

namespace App\Livewire;

use App\Models\Product;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;

class Modal extends Component
{
    public mixed $product = null;
    public string $title;
    public string $subtitle;
    public bool $is_open = false;

    protected $listeners = ['editContent' => 'editContent'];

    /**
     * Edit content event handler
     *
     * @throws Exception
     */
    public function editContent($data): void
    {
        $this->is_open = true;
        $this->product = Product::findOrField($data);
    }

    public function render(): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.modal');
    }
}
