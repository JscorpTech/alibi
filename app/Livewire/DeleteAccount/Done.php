<?php

namespace App\Livewire\DeleteAccount;

use Livewire\Component;

class Done extends Component
{
    public function render()
    {
        return view('livewire.delete-account.done')->layout('components.layouts.user-auth');
    }
}
