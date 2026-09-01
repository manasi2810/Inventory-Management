<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

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
    
            $user = Auth::guard('web')->user();
    
            $token = Str::random(60);
    
            $user->update([
                'session_token' => $token,
            ]);
    
            $request->session()->put('session_token', $token);
    
            return redirect()->intended(
                route('dashboard', absolute: false)
            );
        }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
        {
            $user = Auth::guard('web')->user();
    
            if ($user) {
                $user->update([
                    'session_token' => null,
                ]);
            }
    
            Auth::guard('web')->logout();
    
            $request->session()->invalidate();
    
            $request->session()->regenerateToken();
    
            return redirect('/');
        }
}