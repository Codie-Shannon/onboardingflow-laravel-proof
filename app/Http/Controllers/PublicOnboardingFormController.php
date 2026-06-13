<?php

namespace App\Http\Controllers;

use App\Models\OnboardingInvite;
use App\Models\OnboardingSubmission;
use Illuminate\Http\Request;
use App\Models\MissingInfoItem;

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

        $submission = OnboardingSubmission::create([
            ...$validated,
            'onboarding_invite_id' => $invite->id,
            'raw_payload' => $validated,
        ]);
        
        $missingItems = $this->detectMissingInfo($invite, $submission, $validated);
        
        foreach ($missingItems as $item) {
            MissingInfoItem::create([
                'onboarding_invite_id' => $invite->id,
                'onboarding_submission_id' => $submission->id,
                'field_key' => $item['field_key'],
                'label' => $item['label'],
                'description' => $item['description'],
                'severity' => $item['severity'],
            ]);
        }
        
        $invite->update([
            'status' => count($missingItems) > 0 ? 'needs_info' : 'submitted',
            'submitted_at' => now(),
        ]);

        return view('public.onboarding.thank-you', [
            'invite' => $invite,
        ]);
    }

    private function detectMissingInfo(OnboardingInvite $invite, OnboardingSubmission $submission, array $validated): array
    {
        $checks = [
            [
                'field_key' => 'phone',
                'label' => 'Phone number missing',
                'description' => 'The applicant did not provide a phone number.',
                'severity' => 'warning',
                'value' => $validated['phone'] ?? null,
            ],
            [
                'field_key' => 'organisation',
                'label' => 'Organisation missing',
                'description' => 'The applicant did not provide an organisation.',
                'severity' => 'warning',
                'value' => $validated['organisation'] ?? null,
            ],
            [
                'field_key' => 'role',
                'label' => 'Role / position missing',
                'description' => 'The applicant did not provide their role or position.',
                'severity' => 'warning',
                'value' => $validated['role'] ?? null,
            ],
            [
                'field_key' => 'emergency_contact_name',
                'label' => 'Emergency contact name missing',
                'description' => 'The applicant did not provide an emergency contact name.',
                'severity' => 'important',
                'value' => $validated['emergency_contact_name'] ?? null,
            ],
            [
                'field_key' => 'emergency_contact_phone',
                'label' => 'Emergency contact phone missing',
                'description' => 'The applicant did not provide an emergency contact phone number.',
                'severity' => 'important',
                'value' => $validated['emergency_contact_phone'] ?? null,
            ],
        ];

        return collect($checks)
            ->filter(fn ($check) => blank($check['value']))
            ->map(fn ($check) => [
                'field_key' => $check['field_key'],
                'label' => $check['label'],
                'description' => $check['description'],
                'severity' => $check['severity'],
            ])
            ->values()
            ->all();
    }
}