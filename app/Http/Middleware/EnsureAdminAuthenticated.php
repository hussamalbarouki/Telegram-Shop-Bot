<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureAdminAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->session()->has('admin_id')) {
            return redirect('/admin/login');
        }

        return $next($request);
    }
}
