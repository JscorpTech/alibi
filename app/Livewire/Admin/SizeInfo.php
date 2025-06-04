<?php

namespace App\Livewire\Admin;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use JetBrains\PhpStorm\NoReturn;
use Livewire\Component;

class SizeInfo extends Component
{
    public $data;

    protected $listeners = ['refreshSizeInfo' => 'getData'];

    /**
     * @return void
     */
    public function getData(): void
    {
        $this->data = \App\Models\SizeInfo::query()->orderByDesc('id')->get(); // Get Size info images
    }

    /**
     * Size info image delete
     *
     * @param $id
     * @return void
     */
    #[NoReturn]
    public function delete($id): void
    {
        \App\Models\SizeInfo::query()->where(['id' => $id])->delete();
        $this->dispatch('refreshSizeInfo');
    }

    #[NoReturn]
    public function mount(): void
    {
        $this->getData();
    }

    /**
     * @return View|Application|Factory|\Illuminate\Contracts\Foundation\Application
     *
     */
    public function render(): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.admin.size-info')->layout('components.layouts.main');
    }
}
