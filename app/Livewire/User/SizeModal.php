<?php

namespace App\Livewire\User;

use Livewire\Component;

class SizeModal extends Component
{
    public mixed $product;

    public function render()
    {
        return view('livewire.user.size-modal');
    }
}
