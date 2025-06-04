<?php

namespace App\Observers;

use App\Jobs\NotificationJob;
use App\Models\Notification;

class NotificationObserver
{
    public function created(Notification $notification)
    {
        NotificationJob::dispatch($notification->title, $notification->message,$notification->is_register);
    }
}
