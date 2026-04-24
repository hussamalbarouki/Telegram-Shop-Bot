<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'usersCount' => DB::table('telegram_users')->count(),
            'todayOrders' => DB::table('orders')->whereDate('created_at', now()->toDateString())->count(),
            'sales' => DB::table('orders')->whereIn('status', ['paid', 'processing', 'completed'])->sum('total'),
            'lowStock' => DB::table('products')->where('stock', '<=', 5)->count(),
            'recentOrders' => DB::table('orders')->latest()->limit(8)->get(),
        ]);
    }
}
