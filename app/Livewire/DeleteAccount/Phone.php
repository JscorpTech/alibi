<?php

namespace App\Livewire\DeleteAccount;

use App\Http\Helpers\Helper;
use App\Services\Sms\SmsService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class Phone extends Component
{
    public string $phone = '998 ';
    protected $rules = [
        'phone' => 'required|min:18',
    ];

    public function setPhone(): void
    {
        $this->validate();
        Session::put('phone', Helper::clearPhone($this->phone));
        try {
            SmsService::sendConfirm(Helper::clearPhone($this->phone));
            $this->redirectRoute('delete-account:confirm');
        } catch (\Throwable $e) {
            $this->addError('phone', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.delete-account.phone')->layout('components.layouts.user-auth');
    }
}
