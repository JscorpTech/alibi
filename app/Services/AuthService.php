<?php

namespace App\Services;

use App\Enums\RoleEnum;
use App\Http\Helpers\Helper;
use App\Models\Guest;
use App\Models\User;
use danog\MadelineProto\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthService
{
    /**
     * @throws Exception
     */
    public function login($phone, $password, $remember): bool
    {
        $phone = Helper::clearPhone($phone);

        $user = User::query()->where(['phone' => $phone, 'role' => RoleEnum::ADMIN]);

        if (!$user->exists()) {
            throw new Exception(__('phone:invalid'), code: 1111);
        } elseif (!Hash::check($password, $user->first()->password)) {
            throw new \Mockery\Exception(__('phone:invalid'), code: 1112);
        }
        Auth::login($user->first(), $remember == 'true');

        return true;
    }

    public function guest(Request $request)
    {
        $token = $request->header("Guest-Token");
        if (!$token) {
            return null;
        }
        $guest = Guest::query()->when(['token' => $token]);
        if (!$guest->exists()) {
            return null;
        }
        return $guest->first();
    }

    public function register_guest($name)
    {
        $guest = Guest::query()->create([
            'name' => $name,
            'token' => Str::uuid(),
        ]);
        return $guest;
    }
}
