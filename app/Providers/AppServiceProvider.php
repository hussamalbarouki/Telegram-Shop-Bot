<?php

namespace App\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $key = config('app.key');
        if (! empty($key)) {
            return;
        }

        $preInstallKeyFile = storage_path('framework/preinstall_app.key');

        if (! File::exists($preInstallKeyFile)) {
            File::ensureDirectoryExists(dirname($preInstallKeyFile));
            File::put($preInstallKeyFile, 'base64:'.base64_encode(random_bytes(32)));
        }

        config(['app.key' => trim((string) File::get($preInstallKeyFile))]);
    }
}
