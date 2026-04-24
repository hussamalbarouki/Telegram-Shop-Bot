<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function loginForm(): View
    {
        return view('admin.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $admin = Admin::where('email', $credentials['email'])->where('is_active', true)->first();

        if (! $admin || ! Hash::check($credentials['password'], $admin->password)) {
            return back()->withErrors(['email' => 'بيانات غير صحيحة'])->withInput();
        }

        $request->session()->put('admin_id', $admin->id);
        $admin->update(['last_login_at' => now()]);

        return redirect('/admin');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('admin_id');
        return redirect('/admin/login');
    }
}
