<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\InstallController;
use App\Http\Controllers\TelegramWebhookController;
use App\Support\InstallationStatus;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! InstallationStatus::isInstalled()) {
        return view('errors.not-installed');
    }

    return redirect('/admin/login');
});

Route::middleware('redirect_if_installed')->group(function () {
    Route::get('/install', [InstallController::class, 'index']);
    Route::post('/install', [InstallController::class, 'store']);
});

Route::post('/telegram/webhook/{secret}', TelegramWebhookController::class)->middleware('throttle:120,1');
Route::get('/health', HealthController::class);

Route::prefix('admin')->middleware('ensure_installed')->group(function () {
    Route::get('/login', [AuthController::class, 'loginForm']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('admin.auth')->group(function () {
        Route::get('/', DashboardController::class);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});
