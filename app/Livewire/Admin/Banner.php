<?php

namespace App\Livewire\Admin;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Banner extends Component
{
    use WithFileUploads;

    public $image = null; // Banner image model

    /**
     * Update banner
     *
     * @param $id
     * @param $position
     * @param $status
     * @param $title
     * @param $subtitle
     * @param $link
     * @param $link_text
     * @return RedirectResponse
     */
    public function submit($id, $position, $status, $title, $subtitle, $link, $link_text): RedirectResponse
    {
        $data = [
            'position'  => $position,
            'status'    => $status,
            'title'     => $title,
            'subtitle'  => $subtitle,
            'link'      => $link,
            'link_text' => $link_text,
        ];

        if ($this->image != null) {
            $path = Storage::putFile('banners/', $this->image);
            $data['image'] = $path;
        } // If Image already update

        \App\Models\Banner::query()->where(['id' => $id])->update($data); // Update Banner

        return Redirect::route('banners');
    }

    public function render(): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.admin.banner');
    }
}
