<?php

use App\Http\Middleware\EnsureAdminAuthenticated;
use App\Http\Middleware\EnsureInstalled;
use App\Http\Middleware\RedirectIfInstalled;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'ensure_installed' => EnsureInstalled::class,
            'redirect_if_installed' => RedirectIfInstalled::class,
            'admin.auth' => EnsureAdminAuthenticated::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $installerRoutePatterns = ['install', 'install/*'];

        $exceptions->report(function (\Throwable $exception) use ($installerRoutePatterns) {
            if (! app()->bound('request')) {
                if (! app()->runningInConsole()) {
                    Log::channel('install_errors')->error('Unhandled exception before request binding', [
                        'exception' => get_class($exception),
                        'message' => $exception->getMessage(),
                        'file' => $exception->getFile(),
                        'line' => $exception->getLine(),
                    ]);
                }

                return;
            }

            $request = request();

            $isInstallerRequest = false;
            foreach ($installerRoutePatterns as $pattern) {
                if ($request->is($pattern)) {
                    $isInstallerRequest = true;
                    break;
                }
            }

            if (! $isInstallerRequest) {
                return;
            }

            Log::channel('install_errors')->error('Installer request failed', [
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'ip' => $request->ip(),
                'exception' => get_class($exception),
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ]);
        });
    })->create();
