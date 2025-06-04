<?php

namespace App\Livewire\User;

use App\Models\Size;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;

class SizeCreate extends Component
{
    protected $listeners = ['size-create-modal' => 'handler']; // Listeners

    public bool $is_open = false; // Modal is open
    public bool $is_error = false; // Is error
    public string|null $message = ''; // Error message
    public string $type = 'create'; // Action type Enum: create|update
    public string $name = ''; // Name
    public int|null|string $id = null; // Product id

    public function submit($data): ?bool
    {
        if ($this->type == 'create') {
            $check = Size::query()->where(['name' => $data]);

            if ($check->exists()) {
                $this->is_error = true;
                $this->message = __('size:already');

                return false;
            } else {
                $this->is_error = false;
                $this->message = '';
            }
            Size::query()->create([
                'name' => $data,
            ]);

            $this->is_open = false;
        } elseif ($this->type == 'update') {
            Size::query()->where(['id' => $this->id])->update(['name' => $data]);
            $this->is_error = false;
        }

        return $this->redirect(route('size.index'));
    }

    public function handler($is_open, $name = '', $type = 'create', $id = null)
    {
        $this->is_open = $is_open;
        $this->name = $name;
        $this->type = $type;
        $this->id = $id;
    }

    public function render(): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.user.size-create');
    }
}
