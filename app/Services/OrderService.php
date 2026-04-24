<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\TelegramUser;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function createDirectOrder(TelegramUser $user, Product $product, int $qty, WalletService $walletService): Order
    {
        return DB::transaction(function () use ($user, $product, $qty, $walletService) {
            $total = $qty * (float) $product->price;
            $walletService->adjust($user, -$total, 'purchase', 'شراء منتج: '.$product->name);

            return Order::create([
                'telegram_user_id' => $user->id,
                'status' => in_array($product->type, ['digital_manual', 'normal'], true) ? 'processing' : 'paid',
                'subtotal' => $total,
                'total' => $total,
                'currency' => setting('default_currency', 'USD'),
                'paid_at' => now(),
            ]);
        });
    }
}
