<?php

namespace App\Livewire\Admin;

use App\Enums\GenderEnum;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use JetBrains\PhpStorm\NoReturn;
use Livewire\Component;

class SubCategoryModal extends Component
{
    public mixed $categories;
    public mixed $genders;
    public mixed $gender;

    /**
     * @param $data
     * @param string $type
     * @return void
     * @throws \Exception
     */
    #[NoReturn]
    public function submit($data, string $type = 'create'): void
    {
        if ($type == 'edit') {
            $model = \App\Models\SubCategory::findOrField($data['id']);
            $model->fill([
                'name_ru'     => $data['name'],
                'category_id' => $data['category'],
                'code'        => $data['code'],
                'position'    => $data['position'],
                'gender'      => $data['gender'],
            ]);
            $model->save();
        } else {
            \App\Models\SubCategory::query()->create([
                'name_ru'     => $data['name'],
                'category_id' => $data['category'],
                'code'        => $data['code'],
                'position'    => $data['position'],
                'gender'      => $data['gender'],
            ]);
        }
        $this->dispatch('getSubCategories');
        $this->dispatch('closeModal');
    }

    public function mount(): void
    {
        $this->genders = GenderEnum::toArray();
        $this->categories = \App\Models\SubCategory::query()->get();
    }

    public function render(): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.admin.sub-category-modal');
    }
}
