<?php

namespace App\Services;

use App\Models\TelegramUser;

class NotificationService
{
    public function notifyUser(TelegramUser $user, string $message, TelegramService $telegram): array
    {
        return $telegram->sendMessage($user->telegram_id, $message);
    }
}
