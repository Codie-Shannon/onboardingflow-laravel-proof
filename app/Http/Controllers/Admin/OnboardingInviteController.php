<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OnboardingInvite;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OnboardingInviteController extends Controller
{
    public function index()
    {
        $invites = OnboardingInvite::latest()->paginate(10);

        return view('admin.onboarding.invites.index', [
            'invites' => $invites,
        ]);
    }

    public function create()
    {
        return view('admin.onboarding.invites.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'recipient_name' => ['required', 'string', 'max:255'],
            'recipient_email' => ['required', 'email', 'max:255'],
            'role' => ['nullable', 'string', 'max:255'],
            'organisation' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        $invite = OnboardingInvite::create([
            ...$validated,
            'token' => Str::random(48),
            'status' => 'sent',
            'expires_at' => now()->addDays(7),
        ]);

        return redirect()
            ->route('admin.onboarding.invites.show', $invite)
            ->with('success', 'Onboarding invite created.');
    }

    public function show(OnboardingInvite $invite)
    {
        $publicUrl = route('public.onboarding.show', $invite->token);
    
        return view('admin.onboarding.invites.show', [
            'invite' => $invite,
            'publicUrl' => $publicUrl,
            'statuses' => OnboardingInvite::statuses(),
        ]);
    }

    public function updateStatus(Request $request, OnboardingInvite $invite)
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:sent,started,submitted,in_review,needs_info,approved,rejected,expired'],
        ]);

        $invite->update([
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('admin.onboarding.invites.show', $invite)
            ->with('success', 'Onboarding status updated.');
    }
}