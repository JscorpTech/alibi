<?php

namespace App\Services;

use Illuminate\Support\Env;
use Illuminate\Support\Facades\Log;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use SergiX44\Nutgram\Nutgram;

class BotService
{
    protected Nutgram $bot;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct()
    {
        $token = Env::get('TELEGRAM_TOKEN');
        $this->bot = new Nutgram($token);
    }

    /**
     * Отправка сообщения с автонормализацией chat_id
     */
    public function sendMessage(?string $chatId, string $message): void
    {
        $chatId = $this->normalizeChannelId($chatId);

        try {
            $this->bot->sendMessage($message, $chatId, parse_mode: 'HTML');
            Log::info('Telegram message sent: '.$message);
        } catch (\Throwable $e) {
            Log::error('Telegram send failed: '.$e->getMessage());
        }
    }

    /**
     * Автоматическая нормализация chat_id (чтобы не было ошибок "chat not found")
     */
    private function normalizeChannelId(?string $id): ?string
    {
        if (!$id) return null;
        $id = trim((string)$id);

        // если id начинается с 100..., добавляем -100
        if (!str_starts_with($id, '-100') && preg_match('/^100\d+$/', $id)) {
            return '-'.$id;
        }

        // если просто цифры без минуса — добавляем минус
        if (!str_starts_with($id, '-') && ctype_digit($id)) {
            return '-'.$id;
        }

        return $id;
    }
}