<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Settings;
use Livewire\Component;

class DataList extends Component
{
    public $data;
    public $key;
    public $value;
    public $type;

    public function getData(): void
    {
        $this->data = Settings::query()->orderByDesc('id')->get();
    }

    public function edit($id): void
    {
        $this->type = 'update';
        $data = Settings::findOrField($id);
        $this->key = $data->key;
        $this->value = $data->value;
    }

    public function refresh(): void
    {
        $this->key = '';
        $this->value = '';
        $this->getData();
        $this->type = null;
    }

    public function submit(): void
    {
        $this->validate([
            'key'   => ['required', 'string', 'max:255'],
            'value' => ['required', 'string', 'max:2000'],
        ]);
        $check = Settings::query()->where(['key' => $this->key]);
        if ($check->exists()) {
            $check->update(['value' => $this->value]);
        } else {
            Settings::query()->create([
                'key'   => $this->key,
                'value' => $this->value,
            ]);
        }
        $this->refresh();
    }

    public function delete($id): void
    {
        Settings::query()->where(['id' => $id])->delete();
        $this->refresh();
    }

    public function mount(): void
    {
        $this->getData();
    }

    public function render()
    {
        return view('livewire.admin.settings.data-list')->layout('components.layouts.main');
    }
}
