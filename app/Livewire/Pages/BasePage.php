<?php

namespace App\Livewire\Pages;

use App\Models\Pages;
use Livewire\Component;

class BasePage extends Component
{
    public string|int $page;
    public object $obj;

    public function mount()
    {
        $this->obj = Pages::query()->where(['path' => $this->page]);
        if (!$this->obj->exists()) {
            return $this->redirect('/');
        }
        $this->obj = $this->obj->first();
    }

    public function render()
    {
        return view('livewire.pages.base')->layout('components.layouts.user');
    }
}
