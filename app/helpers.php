<?php

use App\Models\Setting;

if (! function_exists('setting')) {
    function setting(string $key, $default = null)
    {
        static $cache = [];

        if (array_key_exists($key, $cache)) {
            return $cache[$key];
        }

        $value = Setting::query()->where('key', $key)->value('value');

        return $cache[$key] = $value ?? $default;
    }
}
