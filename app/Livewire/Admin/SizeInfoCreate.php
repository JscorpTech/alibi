<?php

namespace App\Livewire\Admin;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class SizeInfoCreate extends Component
{
    use WithFileUploads;

    public $image;
    public $image2;
    public string $name;

    public array $rules = [
        'image'  => ['required', 'file', 'max:10240'],
        'image2' => ['required', 'file', 'max:10240'],
        'name'   => ['required', 'string', 'min:1', 'unique:size_infos,name'],
    ];

    public function submit(): void
    {
        $this->validate();

        $image = Storage::putFile('sizes/', $this->image);
        $image2 = Storage::putFile('sizes/', $this->image2);

        \App\Models\SizeInfo::query()->create([
            'image_1' => $image,
            'image_2' => $image2,
            'name'    => $this->name,
        ]);
        $this->dispatch('closeModal');
        $this->dispatch('refreshSizeInfo');

        $this->image2 = null;
        $this->image = null;
        $this->name = '';
    }

    public function render(): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.admin.size-info-create');
    }
}
