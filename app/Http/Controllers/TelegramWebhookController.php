<?php

namespace App\Http\Controllers;

use App\Services\BotUpdateHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TelegramWebhookController extends Controller
{
    public function __invoke(Request $request, string $secret, BotUpdateHandler $handler): JsonResponse
    {
        if ($secret !== setting('telegram_webhook_secret', env('TELEGRAM_WEBHOOK_SECRET'))) {
            return response()->json(['ok' => false], 403);
        }

        $expectedHeader = setting('telegram_webhook_secret', env('TELEGRAM_WEBHOOK_SECRET'));
        $header = $request->header('X-Telegram-Bot-Api-Secret-Token');
        if ($expectedHeader && $header && ! hash_equals($expectedHeader, $header)) {
            return response()->json(['ok' => false], 403);
        }

        $payload = $request->all();

        DB::table('webhook_logs')->insert([
            'telegram_update_id' => $payload['update_id'] ?? null,
            'payload' => json_encode($payload),
            'status' => 'received',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $handler->handle($payload);

        return response()->json(['ok' => true]);
    }
}
