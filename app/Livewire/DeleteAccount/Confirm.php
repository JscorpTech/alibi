<?php

namespace App\Livewire\DeleteAccount;

use App\Models\User;
use App\Services\Sms\SmsService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Livewire\Component;

class Confirm extends Component
{
    public string $error;

    public function resend(): void
    {
        try {
            SmsService::sendConfirm(Session::get('phone'));
        } catch (\Throwable $e) {
            $this->addError('error', $e->getMessage());
        }
    }

    public function confirm($otp)
    {
        $otp = implode('', array_values($otp));
        $phone = Session::get('phone');
        try {
            $res = SmsService::checkConfirm($phone, $otp);
            if ($res) {
                $user = User::query()->where(['phone' => $phone]);
                $user->delete();
                $this->redirectRoute('delete-account:done');
            }
        } catch (\Throwable $e) {
            $this->addError('error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.delete-account.confirm')->layout('components.layouts.user-auth');
    }
}
