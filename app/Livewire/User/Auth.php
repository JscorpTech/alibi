<?php

namespace App\Livewire\User;

use App\Http\Helpers\Helper;
use App\Models\User;
use App\Services\Sms\SmsService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Livewire\Component;

class Auth extends Component
{
    public string $message = '';
    public object $messages;
    public object $errors;
    public bool $success = false;
    public mixed $previous;

    public function mount(): void
    {
        $this->previous = URL::previous();
        $this->errors = (object) [
            'login'    => false,
            'register' => false,
            'confirm'  => false,
        ];
        $this->messages = (object) [
            'login'    => '',
            'register' => '',
            'confirm'  => '',
        ];
    }

    public function login($phone, $password): mixed
    {
        $phone = Helper::clearPhone($phone);

        if (!Helper::checkPhone($phone)) {
            $this->setError(__('invalid:phone'), 'login');

            return null;
        }

        $user = User::query()->where(['phone' => $phone]);

        if (!$user->exists()) {
            $this->errors->login = true;
            $this->messages->login = __('user:invalid');

            return null;
        }
        $user = $user->first();

        if ($user->verified_at == null) {
            try {
                SmsService::sendConfirm($phone, provider: 'sms');
                Session::put('phone', $phone);
                $this->dispatch('edit-page', ['page' => 'confirm']);
            } catch (\Throwable $e) {
                $this->messages->login = __($e->getMessage());
                $this->errors->login = true;
            }

            return null;
        }
        if (!Hash::check($password, $user->password)) {
            $this->errors->login = true;
            $this->messages->login = __('user:invalid');

            return null;
        }

        $this->errors->login = false;
        $this->messages->login = '';
        \Illuminate\Support\Facades\Auth::login($user, true);

        return $this->redirect($this->previous == URL::current() ? route('home') : $this->previous);
    }

    public function setError($message, $page): void
    {
        $this->messages->$page = $message;
        $this->errors->$page = true;
    }

    public function register($phone, $name, $password, $password_confirm): void
    {
        $phone = Helper::clearPhone($phone);

        if (!Helper::checkPhone($phone)) {
            $this->setError(__('invalid:phone'), 'register');

            return;
        }

        $check = User::withTrashed()->where(['phone' => $phone]);

        if ($password != $password_confirm) {
            $this->errors->register = true;
            $this->messages->register = __('confirm:password:invalid');

            return;
        }

        if ($check->exists()) {
            $this->errors->register = true;

            if ($check->first()->verified_at == null) {
                try {
                    SmsService::sendConfirm($phone);
                    Session::put('phone', $phone);
                    $check->first()->update([
                        'full_name' => $name,
                        'phone'     => $phone,
                        'password'  => Hash::make($password),
                    ]);
                    $this->dispatch('edit-page', ['page' => 'confirm']);
                } catch (\Throwable $e) {
                    $this->messages->register = __($e->getMessage());
                }
            } else {
                $this->messages->register = __('user:already');
            }

            return;
        }

        try {
            SmsService::sendConfirm($phone, provider: 'sms');
        } catch (\Throwable $e) {
            $this->messages->register = __('user:already');
        }
        $this->errors->register = false;

        $user = User::query()->create([
            'full_name' => $name,
            'phone'     => $phone,
            'password'  => Hash::make($password),
        ]);

        $this->dispatch('edit-page', ['page' => 'confirm']);

        Session::put('phone', $phone);
    }

    public function confirm($otp)
    {
        $otp = implode('', array_values($otp));
        $phone = Session::get('phone');

        try {
            $res = SmsService::checkConfirm($phone, $otp);

            if ($res) {
                $user = User::query()->where(['phone' => $phone])->first();
                $user->verified_at = Carbon::now();
                $user->save();

                \Illuminate\Support\Facades\Auth::login($user);

                return $this->redirect($this->previous == URL::current() ? route('home') : $this->previous);
            }
        } catch (\Throwable $e) {
            $this->messages->confirm = __($e->getMessage());
        }
        $this->errors->confirm = true;
    }

    public function resend(): void
    {
        try {
            SmsService::sendConfirm(Session::get('phone'));
        } catch (\Throwable $e) {
            $this->messages->confirm = __($e->getMessage());
            $this->errors->confirm = true;
        }
    }

    public function render()
    {
        return view('livewire.user.auth')->layout('components.layouts.user-auth');
    }
}
