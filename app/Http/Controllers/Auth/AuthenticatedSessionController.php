<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\User;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        /** @var User $user */
        $user = Auth::user();

        // Generate switch token - disimpan hashed di database
        // Raw token disimpan di session untuk keperluan frontend saja
        // (Token dikirim ke frontend dan disimpan di localStorage untuk account switching)
        $rawToken = SwitchAccountController::generateToken($user);
        $request->session()->put('switch_token_raw', $rawToken);

        // Jika admin, cek apakah sudah pilih role
        if ($user->isAdmin()) {
            if (!$user->hasSelectedRole()) {
                return redirect()->intended(route('admin.role.select'));
            }
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
