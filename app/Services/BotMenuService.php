<?php

namespace App\Services;

class BotMenuService
{
    public function mainMenu(): array
    {
        return [
            'inline_keyboard' => [
                [['text' => '🛍 المتجر', 'callback_data' => 'store'], ['text' => '🧾 طلباتي', 'callback_data' => 'orders']],
                [['text' => '💰 محفظتي', 'callback_data' => 'wallet'], ['text' => '🔎 بحث', 'callback_data' => 'search']],
                [['text' => '🆘 الدعم', 'callback_data' => 'support'], ['text' => '⚙️ اللغة', 'callback_data' => 'lang']],
            ],
        ];
    }
}
