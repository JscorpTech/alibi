<?php

namespace App\Services;

use Illuminate\Support\Env;
use Illuminate\Support\Facades\Log;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use SergiX44\Nutgram\Nutgram;

class BotService
{
    public $bot;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct()
    {
        $this->bot = new Nutgram(Env::get('TELEGRAM_TOKEN'));
    }

    public function sendMessage($chatId, $message): void
    {
        $this->bot->sendMessage($message, $chatId, parse_mode: 'HTML');
        Log::info($message);
    }
}
