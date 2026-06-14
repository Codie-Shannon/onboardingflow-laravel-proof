<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\DocumentRequirement;
use App\Models\MissingInfoFollowUp;
use App\Models\MissingInfoItem;
use App\Models\OnboardingInvite;
use App\Models\OnboardingSubmission;
use App\Services\MicrosoftSharePointDocumentService;
use Illuminate\Http\Request;
use Throwable;

class PublicOnboardingFormController extends Controller
{
    public function show(string $token)
    {
        $invite = OnboardingInvite::where('token', $token)
            ->with([
                'template',
                'submission',
                'missingInfoItems.followUps',
                'missingInfoFollowUps.missingInfoItem',
                'documentRequirements',
            ])
            ->firstOrFail();

        if ($invite->expires_at && now()->greaterThan($invite->expires_at)) {
            if ($invite->status !== 'expired') {
                $invite->update([
                    'status' => 'expired',
                ]);
            }

            return view('public.onboarding.expired', [
                'invite' => $invite,
            ]);
        }

        $isResubmission = $invite->submission && $invite->status === 'needs_info';

        if ($invite->submission && ! $isResubmission) {
            return view('public.onboarding.already-submitted', [
                'invite' => $invite,
            ]);
        }

        if ($invite->status === 'sent') {
            $invite->update([
                'status' => 'started',
            ]);

            ActivityLog::create([
                'onboarding_invite_id' => $invite->id,
                'actor_name' => 'Applicant',
                'action' => 'onboarding_started',
                'description' => 'Applicant opened the onboarding form.',
            ]);
        }

        return view('public.onboarding.show', [
            'invite' => $invite,
            'isResubmission' => $isResubmission,
        ]);
    }

    public function store(
        Request $request,
        string $token,
        MicrosoftSharePointDocumentService $documentService
    ) {
        $invite = OnboardingInvite::where('token', $token)
            ->with([
                'template',
                'submission',
                'missingInfoItems.followUps',
                'missingInfoFollowUps.missingInfoItem',
                'documentRequirements',
            ])
            ->firstOrFail();

        if ($invite->expires_at && now()->greaterThan($invite->expires_at)) {
            if ($invite->status !== 'expired') {
                $invite->update([
                    'status' => 'expired',
                ]);
            }

            return redirect()
                ->route('public.onboarding.show', $invite->token);
        }

        $isResubmission = $invite->submission && $invite->status === 'needs_info';

        if ($invite->submission && ! $isResubmission) {
            return redirect()
                ->route('public.onboarding.show', $invite->token);
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
            'documents' => ['nullable', 'array'],
            'documents.*' => ['nullable', 'file', 'max:10240'],
        ]);

        $submissionData = [
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'organisation' => $validated['organisation'] ?? null,
            'role' => $validated['role'] ?? null,
            'emergency_contact_name' => $validated['emergency_contact_name'] ?? null,
            'emergency_contact_phone' => $validated['emergency_contact_phone'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ];

        if ($isResubmission) {
            $submission = $invite->submission;

            $submission->update($submissionData);
        } else {
            $submission = OnboardingSubmission::create(array_merge($submissionData, [
                'onboarding_invite_id' => $invite->id,
            ]));
        }

        $this->handleDocumentUploads(
            request: $request,
            invite: $invite,
            documentService: $documentService
        );

        if ($isResubmission) {
            $this->autoResolveMissingInfoItems($invite, $submission);

            $invite->update([
                'status' => 'in_review',
                'resubmitted_at' => now(),
                'resubmission_count' => (int) ($invite->resubmission_count ?? 0) + 1,
            ]);

            ActivityLog::create([
                'onboarding_invite_id' => $invite->id,
                'actor_name' => 'Applicant',
                'action' => 'applicant_resubmitted',
                'description' => 'Applicant resubmitted onboarding information after a needs-info request.',
            ]);
        } else {
            $missingInfoItems = $this->createMissingInfoItems($invite, $submission);

            $invite->update([
                'status' => $missingInfoItems > 0 ? 'needs_info' : 'submitted',
                'submitted_at' => now(),
            ]);

            ActivityLog::create([
                'onboarding_invite_id' => $invite->id,
                'actor_name' => 'Applicant',
                'action' => 'onboarding_submitted',
                'description' => $missingInfoItems > 0
                    ? 'Applicant submitted onboarding information with missing information detected.'
                    : 'Applicant submitted onboarding information.',
            ]);
        }

        return view('public.onboarding.thank-you', [
            'invite' => $invite->fresh(),
            'isResubmission' => $isResubmission,
        ]);
    }

    private function createMissingInfoItems(OnboardingInvite $invite, OnboardingSubmission $submission): int
    {
        $requiredFields = [
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'organisation' => 'Organisation',
            'role' => 'Role / Position',
            'emergency_contact_name' => 'Emergency Contact Name',
            'emergency_contact_phone' => 'Emergency Contact Phone',
        ];

        $createdCount = 0;

        foreach ($requiredFields as $fieldKey => $fieldLabel) {
            if (! blank($submission->{$fieldKey})) {
                continue;
            }

            MissingInfoItem::create([
                'onboarding_invite_id' => $invite->id,
                'onboarding_submission_id' => $submission->id,
                'field_key' => $fieldKey,
                'field_name' => $fieldLabel,
                'label' => $fieldLabel,
                'resolved' => false,
            ]);

            $createdCount++;
        }

        foreach ($invite->documentRequirements as $requirement) {
            if ($requirement->status !== 'missing') {
                continue;
            }
        
            MissingInfoItem::create([
                'onboarding_invite_id' => $invite->id,
                'onboarding_submission_id' => $submission->id,
                'field_key' => 'document_requirement_' . $requirement->id,
                'field_name' => $requirement->label,
                'label' => "Document required: {$requirement->label}",
                'resolved' => false,
            ]);
        
            $createdCount++;
        }

        return $createdCount;
    }

    private function handleDocumentUploads(
        Request $request,
        OnboardingInvite $invite,
        MicrosoftSharePointDocumentService $documentService
    ): void {
        foreach ($invite->documentRequirements as $requirement) {
            $file = $request->file("documents.{$requirement->id}");

            if (! $file) {
                continue;
            }

            try {
                $upload = $documentService->uploadRequirementDocument(
                    invite: $invite,
                    requirement: $requirement,
                    file: $file
                );

                $requirement->update([
                    'status' => 'provided',
                    'sharepoint_drive_id' => $upload['drive_id']
                        ?? $upload['sharepoint_drive_id']
                        ?? config('onboarding.sharepoint.drive_id'),
                
                    'sharepoint_item_id' => $upload['item_id']
                        ?? $upload['sharepoint_item_id']
                        ?? $upload['id']
                        ?? null,
                
                    'sharepoint_web_url' => $upload['web_url']
                        ?? $upload['sharepoint_web_url']
                        ?? $upload['webUrl']
                        ?? null,
                
                    'uploaded_original_name' => $upload['original_name']
                        ?? $upload['uploaded_original_name']
                        ?? $file->getClientOriginalName(),
                
                    'uploaded_mime_type' => $upload['mime_type']
                        ?? $upload['uploaded_mime_type']
                        ?? $file->getClientMimeType(),
                
                    'uploaded_size' => $upload['size']
                        ?? $upload['uploaded_size']
                        ?? $file->getSize(),
                
                    'uploaded_at' => now(),
                    'upload_error' => null,
                ]);

                $this->resolveDocumentMissingInfoItem($invite, $requirement);

                ActivityLog::create([
                    'onboarding_invite_id' => $invite->id,
                    'actor_name' => 'Applicant',
                    'action' => 'document_uploaded',
                    'description' => "Uploaded document for \"{$requirement->label}\".",
                ]);
            } catch (Throwable $exception) {
                $requirement->update([
                    'upload_error' => str($exception->getMessage())->limit(1500)->toString(),
                ]);

                ActivityLog::create([
                    'onboarding_invite_id' => $invite->id,
                    'actor_name' => 'Applicant',
                    'action' => 'document_upload_failed',
                    'description' => "Upload failed for document requirement \"{$requirement->label}\".",
                ]);
            }
        }
    }

    private function autoResolveMissingInfoItems(OnboardingInvite $invite, OnboardingSubmission $submission): void
    {
        $fieldMap = [
            'first_name' => $submission->first_name,
            'last_name' => $submission->last_name,
            'email' => $submission->email,
            'phone' => $submission->phone,
            'organisation' => $submission->organisation,
            'role' => $submission->role,
            'emergency_contact_name' => $submission->emergency_contact_name,
            'emergency_contact_phone' => $submission->emergency_contact_phone,
            'notes' => $submission->notes,
        ];

        $items = MissingInfoItem::where('onboarding_invite_id', $invite->id)
            ->where('resolved', false)
            ->whereNotNull('field_key')
            ->get();

        foreach ($items as $item) {
            $value = $fieldMap[$item->field_key] ?? null;

            if (blank($value)) {
                continue;
            }

            $item->update([
                'resolved' => true,
                'resolved_at' => now(),
            ]);

            MissingInfoFollowUp::where('onboarding_invite_id', $invite->id)
                ->where('missing_info_item_id', $item->id)
                ->where('status', 'open')
                ->update([
                    'status' => 'resolved',
                    'resolved_by' => 'Applicant',
                    'resolved_at' => now(),
                ]);

            ActivityLog::create([
                'onboarding_invite_id' => $invite->id,
                'actor_name' => 'Applicant',
                'action' => 'missing_info_auto_resolved',
                'description' => "Missing info item \"{$item->label}\" was resolved during applicant resubmission.",
            ]);
        }
    }

    private function resolveDocumentMissingInfoItem(OnboardingInvite $invite, DocumentRequirement $requirement): void
    {
        $items = MissingInfoItem::where('onboarding_invite_id', $invite->id)
            ->where('resolved', false)
            ->where('field_key', 'document_requirement_' . $requirement->id)
            ->get();
    
        foreach ($items as $item) {
            $item->update([
                'resolved' => true,
                'resolved_at' => now(),
            ]);
    
            MissingInfoFollowUp::where('onboarding_invite_id', $invite->id)
                ->where('missing_info_item_id', $item->id)
                ->where('status', 'open')
                ->update([
                    'status' => 'resolved',
                    'resolved_by' => 'Applicant',
                    'resolved_at' => now(),
                ]);
    
            ActivityLog::create([
                'onboarding_invite_id' => $invite->id,
                'actor_name' => 'Applicant',
                'action' => 'missing_info_auto_resolved',
                'description' => "Document missing info item \"{$item->label}\" was resolved during applicant upload.",
            ]);
        }
    }
}