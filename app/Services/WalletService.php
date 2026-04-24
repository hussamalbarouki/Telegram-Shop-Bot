<?php

namespace App\Services;

use App\Models\TelegramUser;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;

class WalletService
{
    public function walletFor(TelegramUser $user): Wallet
    {
        return Wallet::firstOrCreate(['telegram_user_id' => $user->id], ['currency' => setting('default_currency', 'USD')]);
    }

    public function adjust(TelegramUser $user, float $amount, string $type, ?string $note = null): Wallet
    {
        return DB::transaction(function () use ($user, $amount, $type, $note) {
            $wallet = $this->walletFor($user);
            $before = (float) $wallet->balance;
            $after = $before + $amount;
            if ($after < 0) {
                throw new \RuntimeException('رصيد غير كافٍ');
            }
            $wallet->update(['balance' => $after]);

            WalletTransaction::create([
                'telegram_user_id' => $user->id,
                'type' => $type,
                'amount' => $amount,
                'balance_before' => $before,
                'balance_after' => $after,
                'note' => $note,
            ]);

            return $wallet->fresh();
        });
    }
}
