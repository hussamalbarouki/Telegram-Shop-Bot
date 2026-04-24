<?php

namespace App\Http\Controllers;

use App\Services\TelegramService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class HealthController extends Controller
{
    public function __invoke(TelegramService $telegram): JsonResponse
    {
        $dbOk = true;
        try { DB::select('select 1'); } catch (\Throwable) { $dbOk = false; }

        $writable = collect(['storage', 'bootstrap/cache', 'database', 'public/uploads'])
            ->mapWithKeys(fn ($p) => [$p => is_writable(base_path($p))]);

        $webhook = $telegram->webhookInfo();

        return response()->json([
            'app' => 'ok',
            'db' => $dbOk ? 'ok' : 'failed',
            'writable_dirs' => $writable,
            'webhook_status' => $webhook['ok'] ?? false,
        ]);
    }
}
