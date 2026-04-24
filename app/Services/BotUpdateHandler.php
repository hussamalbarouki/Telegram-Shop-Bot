<?php

namespace App\Services;

use App\Models\TelegramUser;

class BotUpdateHandler
{
    public function __construct(
        private TelegramService $telegram,
        private BotMenuService $menu,
        private WalletService $walletService,
    ) {}

    public function handle(array $update): void
    {
        $msg = $update['message'] ?? null;
        $cb = $update['callback_query'] ?? null;

        if ($msg) {
            $this->handleMessage($msg);
            return;
        }

        if ($cb) {
            $this->handleCallback($cb);
        }
    }

    private function upsertUser(array $from): TelegramUser
    {
        $user = TelegramUser::firstOrCreate(
            ['telegram_id' => $from['id']],
            [
                'username' => $from['username'] ?? null,
                'first_name' => $from['first_name'] ?? null,
                'last_name' => $from['last_name'] ?? null,
                'language_code' => $from['language_code'] ?? null,
                'is_bot' => (bool) ($from['is_bot'] ?? false),
                'raw_json' => $from,
                'first_seen_at' => now(),
                'last_seen_at' => now(),
            ]
        );

        $user->update([
            'username' => $from['username'] ?? $user->username,
            'first_name' => $from['first_name'] ?? $user->first_name,
            'last_name' => $from['last_name'] ?? $user->last_name,
            'language_code' => $from['language_code'] ?? $user->language_code,
            'raw_json' => $from,
            'last_seen_at' => now(),
        ]);

        return $user->fresh();
    }

    private function handleMessage(array $message): void
    {
        $chatId = $message['chat']['id'];
        $user = $this->upsertUser($message['from']);

        if ($user->is_blocked) {
            $this->telegram->sendMessage($chatId, 'تم حظرك من استخدام المتجر.');
            return;
        }

        if (isset($message['contact'])) {
            $user->update(['phone_number' => $message['contact']['phone_number'] ?? null]);
            $this->telegram->sendMessage($chatId, '✅ تم التفعيل بنجاح.', $this->menu->mainMenu());
            return;
        }

        if (($message['text'] ?? '') === '/start') {
            if (! $user->phone_number) {
                $this->telegram->request('sendMessage', [
                    'chat_id' => $chatId,
                    'text' => setting('welcome_phone_message', 'شارك رقم هاتفك للمتابعة.'),
                    'reply_markup' => json_encode(['keyboard' => [[['text' => '📱 مشاركة الرقم', 'request_contact' => true]]], 'resize_keyboard' => true, 'one_time_keyboard' => true]),
                ]);
                return;
            }

            $this->telegram->sendMessage($chatId, setting('welcome_message', 'أهلاً بك في المتجر'), $this->menu->mainMenu());
        }
    }

    private function handleCallback(array $callback): void
    {
        $chatId = $callback['message']['chat']['id'] ?? null;
        $data = $callback['data'] ?? '';
        if (! $chatId) {
            return;
        }

        if ($data === 'wallet') {
            $user = TelegramUser::where('telegram_id', $callback['from']['id'])->first();
            if (! $user) {
                return;
            }
            $wallet = $this->walletService->walletFor($user);
            $this->telegram->sendMessage($chatId, "رصيدك الحالي: {$wallet->balance} {$wallet->currency}");
        } elseif ($data === 'search') {
            $this->telegram->sendMessage($chatId, 'أرسل كلمة البحث الآن.');
        } else {
            $this->telegram->sendMessage($chatId, 'تم استلام طلبك.');
        }

        $this->telegram->request('answerCallbackQuery', [
            'callback_query_id' => $callback['id'],
            'text' => 'OK',
        ]);
    }
}
