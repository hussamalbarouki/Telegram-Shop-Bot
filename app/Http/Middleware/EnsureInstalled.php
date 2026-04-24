<?php

namespace App\Http\Middleware;

use App\Support\InstallationStatus;
use Closure;
use Illuminate\Http\Request;

class EnsureInstalled
{
    public function handle(Request $request, Closure $next)
    {
        if (! InstallationStatus::isInstalled() && ! $request->is('install*')) {
            return redirect('/install');
        }

        return $next($request);
    }
}
