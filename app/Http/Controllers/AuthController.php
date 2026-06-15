<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Theme;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            if (! auth()->user()->isAdmin()) {
                Auth::logout();

                return back()->withErrors(['email' => 'Tài khoản không có quyền admin.']);
            }

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors(['email' => 'Email hoặc mật khẩu không đúng.'])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
