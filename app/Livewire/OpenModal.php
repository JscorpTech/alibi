<?php

namespace App\Livewire;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;

class OpenModal extends Component
{
    public string $params;
    public string $text;
    public string $class;
    public string|null $emit = null;
    public string|null $tag = 'div';

    /**
     * Component mounted event
     *
     * @param $params
     * @param $text
     * @return void
     */
    public function mount($params, $text = null): void
    {
        $this->text = $text;
        $this->params = $params;
    }

    public function render(): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.open-modal');
    }
}
