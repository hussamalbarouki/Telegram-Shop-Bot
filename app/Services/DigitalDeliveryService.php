<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class DigitalDeliveryService
{
    public function reserveAndDeliver(int $productId, int $userId, int $orderId): ?array
    {
        return DB::transaction(function () use ($productId, $userId, $orderId) {
            $item = DB::table('digital_delivery_items')
                ->where('product_id', $productId)
                ->where('status', 'available')
                ->lockForUpdate()
                ->first();

            if (! $item) {
                return null;
            }

            DB::table('digital_delivery_items')->where('id', $item->id)->update([
                'status' => 'delivered',
                'delivered_to_user_id' => $userId,
                'delivered_order_id' => $orderId,
                'delivered_at' => now(),
                'updated_at' => now(),
            ]);

            return (array) $item;
        });
    }
}
