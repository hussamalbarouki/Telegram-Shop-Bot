<?php

namespace App\Http\Controllers;

use App\Services\InstallerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InstallController extends Controller
{
    public function __construct(private InstallerService $installer) {}

    public function index(): View
    {
        return view('install.index', ['requirements' => $this->installer->requirements()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'system_name' => 'required|string|max:255',
            'app_url' => 'required|url',
            'bot_token' => 'required|string|max:255',
            'webhook_secret' => 'required|string|max:120',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|max:255',
            'admin_password' => 'required|string|min:8|max:255',
            'currency' => 'required|string|max:10',
            'language' => 'required|string|max:10',
            'timezone' => 'required|string|max:120',
        ]);

        $this->installer->install($data);

        return redirect('/admin/login')->with('success', 'تم التثبيت بنجاح');
    }
}
