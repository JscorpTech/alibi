<?php

namespace App\Livewire\DeleteAccount;

use Livewire\Component;

class Info extends Component
{
    public function render()
    {
        return view('livewire.delete-account.info')->layout('components.layouts.user-auth');
    }
}
