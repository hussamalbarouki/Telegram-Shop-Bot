<?php

namespace App\Support;

class InstallationStatus
{
    public static function lockFile(): string
    {
        return storage_path('installed.lock');
    }

    public static function isInstalled(): bool
    {
        return file_exists(self::lockFile());
    }
}
