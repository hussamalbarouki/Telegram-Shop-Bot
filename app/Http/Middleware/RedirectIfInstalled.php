<?php

namespace App\Http\Middleware;

use App\Support\InstallationStatus;
use Closure;
use Illuminate\Http\Request;

class RedirectIfInstalled
{
    public function handle(Request $request, Closure $next)
    {
        if (InstallationStatus::isInstalled()) {
            return redirect('/admin/login');
        }

        return $next($request);
    }
}
