<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\MissingInfoItem;
use App\Models\OnboardingInvite;
use App\Models\OnboardingSubmission;
use Illuminate\Http\Request;

class PublicOnboardingFormController extends Controller
{
    public function show(string $token)
    {
        $invite = OnboardingInvite::with(['template', 'submission'])
            ->where('token', $token)
            ->firstOrFail();

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
        $invite = OnboardingInvite::with(['template', 'submission'])
            ->where('token', $token)
            ->firstOrFail();

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
            'onboarding_invite_id' => $invite->id,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'organisation' => $validated['organisation'] ?? null,
            'role' => $validated['role'] ?? null,
            'emergency_contact_name' => $validated['emergency_contact_name'] ?? null,
            'emergency_contact_phone' => $validated['emergency_contact_phone'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'raw_payload' => $validated,
        ]);

        $missingItems = $this->detectMissingInfo($invite, $submission);

        foreach ($missingItems as $item) {
            MissingInfoItem::create([
                'onboarding_invite_id' => $invite->id,
                'onboarding_submission_id' => $submission->id,
                'field_name' => $item['field_name'],
                'label' => $item['label'],
                'description' => $item['description'],
                'severity' => $item['severity'],
                'resolved' => false,
            ]);
        }

        $invite->update([
            'status' => count($missingItems) > 0 ? 'needs_info' : 'submitted',
            'submitted_at' => now(),
        ]);

        ActivityLog::create([
            'onboarding_invite_id' => $invite->id,
            'actor_name' => $submission->first_name . ' ' . $submission->last_name,
            'action' => 'form_submitted',
            'description' => count($missingItems) > 0
                ? 'Public onboarding form submitted with missing information.'
                : 'Public onboarding form submitted.',
        ]);

        return view('public.onboarding.thank-you', [
            'invite' => $invite,
            'submission' => $submission,
        ]);
    }

    private function detectMissingInfo(OnboardingInvite $invite, OnboardingSubmission $submission): array
    {
        $missing = [];

        $checks = [
            'phone' => [
                'label' => 'Phone',
                'description' => 'Phone number was not provided.',
                'severity' => 'medium',
            ],
            'organisation' => [
                'label' => 'Organisation',
                'description' => 'Organisation was not provided.',
                'severity' => 'medium',
            ],
            'role' => [
                'label' => 'Role / Position',
                'description' => 'Role or position was not provided.',
                'severity' => 'medium',
            ],
            'emergency_contact_name' => [
                'label' => 'Emergency Contact Name',
                'description' => 'Emergency contact name was not provided.',
                'severity' => 'high',
            ],
            'emergency_contact_phone' => [
                'label' => 'Emergency Contact Phone',
                'description' => 'Emergency contact phone was not provided.',
                'severity' => 'high',
            ],
        ];

        foreach ($checks as $field => $details) {
            if (blank($submission->{$field})) {
                $missing[] = [
                    'field_name' => $field,
                    'label' => $details['label'],
                    'description' => $details['description'],
                    'severity' => $details['severity'],
                ];
            }
        }

        return $missing;
    }
}