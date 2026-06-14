<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => 'The provided credentials do not match an OnboardingFlow user.',
            ]);
        }

        $request->session()->regenerate();

        $request->user()->update([
            'last_login_at' => now(),
        ]);

        ActivityLog::create([
            'actor_name' => $request->user()->name,
            'action' => 'user_logged_in',
            'description' => "{$request->user()->name} logged in as {$request->user()->roleLabel()}.",
        ]);

        return redirect()->intended(route('admin.onboarding.dashboard'));
    }

    public function destroy(Request $request)
    {
        $userName = $request->user()?->name ?? 'User';

        ActivityLog::create([
            'actor_name' => $userName,
            'action' => 'user_logged_out',
            'description' => "{$userName} logged out.",
        ]);

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}