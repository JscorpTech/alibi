<?php

namespace App\Livewire;

use App\Enums\GenderEnum;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class SelectGender extends Component
{
    public bool $select_gender = false;

    protected $listeners = ['selectGender'];

    public function selectGender($gender): mixed
    {
        Session::put('gender', $gender);

        return Redirect::route('home');
    }

    public function mount(): void
    {
        $check = Session::get('gender', false);
        if ($check != GenderEnum::MALE and $check != GenderEnum::FEMALE and Route::current()->getName() == 'home') {
            $this->select_gender = true;
        }
    }

    public function render(): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.select-gender');
    }
}
