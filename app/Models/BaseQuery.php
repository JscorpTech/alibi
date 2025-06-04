<?php

namespace App\Models;

use App\Enums\GenderEnum;
use App\Enums\RoleEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use function request;

trait BaseQuery
{
    public function newQuery(): Builder
    {
        if (Auth::user()?->role != RoleEnum::ADMIN) {
            $request = request();
            $gender = $request->header('gender');

            $gender = $gender ?? Session::get('gender', GenderEnum::MALE);

            return parent::newQuery()->where(['gender' => $gender]);
        }

        return parent::newQuery();
    }
}
