<?php

namespace App\Jobs;

use App\Models\User;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

class NotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $title;
    public $message;
    public $is_register;

    public function __construct($title, $message, $is_register = false)
    {
        $this->title = $title;
        $this->message = $message;
        $this->is_register = $is_register;
    }

    public function handle(): void
    {
        $firebase = (new Factory)->withServiceAccount(base_path("firebase.json"));

        if (!$this->is_register) {
            $this->sendGlobalNotification($firebase);
        } else {
            $this->sendIndividualNotifications($firebase);
        }
    }

    protected function sendGlobalNotification($firebase)
    {
        $messaging = $firebase->createMessaging();
        $message = CloudMessage::fromArray([
            'notification' => [
                'title' => htmlspecialchars_decode($this->title),
                'body' => html_entity_decode(strip_tags(htmlspecialchars_decode($this->message)))
            ],
            'topic' => 'global'
        ]);
        $messaging->send($message);
    }

    protected function sendIndividualNotifications($firebase)
    {

        User::chunk(100, function ($users) use ($firebase) {
            foreach ($users as $user) {
                try {
                    $messaging = $firebase->createMessaging();
                    $token = $user->fcm_token;
                    if ($token !== null or $token != "") {
                        print("sending notification: $token\n");
                        $message = CloudMessage::fromArray([
                            'notification' => [
                                'title' => htmlspecialchars_decode($this->title),
                                'body' => html_entity_decode(strip_tags(htmlspecialchars_decode($this->message)))
                            ],
                            'token' => $token,
                        ]);
                        $response = $messaging->send($message);
                        print("\n===============\n");
                        print($response);
                        print("\n===============\n");
                    }
                } catch (Exception $e) {
                    \Log::error('Error sending notification: ' . $e->getMessage());
                }
            }
        });
    }
}
