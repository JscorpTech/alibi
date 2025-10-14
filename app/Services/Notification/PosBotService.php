<?php

// app/Services/PosBotService.php
namespace App\Services\Notification;

use SergiX44\Nutgram\Nutgram;

class PosBotService
{
    public function __construct(
        private ?string $token = null,
        private ?string $defaultChatId = null,
    ) {
        $this->token = $this->token ?? env('POS_TELEGRAM_TOKEN');
        $this->defaultChatId = $this->defaultChatId ?? env('POS_TELEGRAM_CHAT_ID');
    }

    public function send(?string $chatId, string $message): bool
    {
        try {
            if (empty($this->token)) return false;

            $bot = new Nutgram($this->token);
            $chatId = $this->normalizeChannelId($chatId ?: $this->defaultChatId);
            if (empty($chatId)) return false;

            $bot->sendMessage($message, $chatId, parse_mode: 'HTML');
            return true;
        } catch (\Throwable $e) {
            \Log::error('POS bot send failed: '.$e->getMessage());
            return false;
        }
    }

    private function normalizeChannelId(?string $id): ?string
    {
        if (!$id) return null;
        $id = trim((string)$id);

        // твой кейс: "1003006873253" → "-1003006873253"
        if (!str_starts_with($id, '-100') && preg_match('/^100\d+$/', $id)) {
            return '-'.$id;
        }
        if (!str_starts_with($id, '-') && ctype_digit($id)) {
            return '-'.$id;
        }
        return $id;
    }
}