<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TelegramService
{
    public function request(string $method, array $payload = []): array
    {
        $token = config('services.telegram.bot_token', env('TELEGRAM_BOT_TOKEN'));
        if (! $token) {
            return ['ok' => false, 'description' => 'Bot token missing'];
        }

        return Http::timeout(15)->post("https://api.telegram.org/bot{$token}/{$method}", $payload)->json() ?? ['ok' => false];
    }

    public function sendMessage(int|string $chatId, string $text, ?array $keyboard = null): array
    {
        return $this->request('sendMessage', [
            'chat_id' => $chatId,
            'text' => strip_tags($text),
            'parse_mode' => 'HTML',
            'reply_markup' => $keyboard ? json_encode($keyboard) : null,
        ]);
    }

    public function setWebhook(string $url, string $secret, array $allowedUpdates = ['message', 'callback_query', 'contact']): array
    {
        return $this->request('setWebhook', [
            'url' => $url,
            'secret_token' => $secret,
            'allowed_updates' => json_encode($allowedUpdates),
        ]);
    }

    public function deleteWebhook(): array
    {
        return $this->request('deleteWebhook');
    }

    public function webhookInfo(): array
    {
        return $this->request('getWebhookInfo');
    }
}
