<?php

namespace App\Http\Controllers;

use App\Models\OnboardingInvite;
use App\Models\OnboardingSubmission;
use Illuminate\Http\Request;

class PublicOnboardingFormController extends Controller
{
    public function show(string $token)
    {
        $invite = OnboardingInvite::where('token', $token)->firstOrFail();

        if ($invite->expires_at && $invite->expires_at->isPast()) {
            return view('public.onboarding.expired', [
                'invite' => $invite,
            ]);
        }

        if ($invite->submission) {
            return view('public.onboarding.already-submitted', [
                'invite' => $invite,
            ]);
        }

        return view('public.onboarding.show', [
            'invite' => $invite,
        ]);
    }

    public function store(Request $request, string $token)
    {
        $invite = OnboardingInvite::where('token', $token)->firstOrFail();

        if ($invite->expires_at && $invite->expires_at->isPast()) {
            return view('public.onboarding.expired', [
                'invite' => $invite,
            ]);
        }

        if ($invite->submission) {
            return view('public.onboarding.already-submitted', [
                'invite' => $invite,
            ]);
        }

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'organisation' => ['nullable', 'string', 'max:255'],
            'role' => ['nullable', 'string', 'max:255'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:3000'],
        ]);

        OnboardingSubmission::create([
            ...$validated,
            'onboarding_invite_id' => $invite->id,
            'raw_payload' => $validated,
        ]);

        $invite->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        return view('public.onboarding.thank-you', [
            'invite' => $invite,
        ]);
    }
}