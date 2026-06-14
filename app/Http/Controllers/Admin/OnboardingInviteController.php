<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\MissingInfoItem;
use App\Models\OnboardingInvite;
use App\Models\OnboardingNote;
use App\Models\OnboardingTemplate;
use App\Models\ReviewChecklistItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use App\Models\DocumentRequirement;
use App\Models\MissingInfoFollowUp;
use App\Services\MicrosoftGraphMailService;
use Throwable;

class OnboardingInviteController extends Controller
{
    public function index()
    {
        $invites = OnboardingInvite::with(['template'])
            ->withCount([
                'unresolvedMissingInfoItems',
            ])
            ->latest()
            ->paginate(10);

        return view('admin.onboarding.invites.index', [
            'invites' => $invites,
        ]);
    }

    public function create()
    {
        $templates = OnboardingTemplate::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.onboarding.invites.create', [
            'templates' => $templates,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'onboarding_template_id' => ['nullable', 'exists:onboarding_templates,id'],
            'recipient_name' => ['required', 'string', 'max:255'],
            'recipient_email' => ['required', 'email', 'max:255'],
            'role' => ['nullable', 'string', 'max:255'],
            'organisation' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        $template = null;

        if (! empty($validated['onboarding_template_id'])) {
            $template = OnboardingTemplate::find($validated['onboarding_template_id']);
        }

        $expiryDays = $template?->default_expiry_days ?? 7;

        $invite = OnboardingInvite::create([
            'onboarding_template_id' => $validated['onboarding_template_id'] ?? null,
            'recipient_name' => $validated['recipient_name'],
            'recipient_email' => $validated['recipient_email'],
            'role' => $validated['role'] ?? null,
            'organisation' => $validated['organisation'] ?? null,
            'message' => $validated['message'] ?? null,
            'token' => Str::random(48),
            'status' => 'sent',
            'expires_at' => now()->addDays($expiryDays),
        ]);

        $this->createReviewChecklistItemsFromTemplate($invite);
        $this->createDocumentRequirementsFromTemplate($invite);

        ActivityLog::create([
            'onboarding_invite_id' => $invite->id,
            'actor_name' => 'Admin User',
            'action' => 'invite_created',
            'description' => $template
                ? "Onboarding invite created for {$invite->recipient_name} using {$template->name}."
                : "Onboarding invite created for {$invite->recipient_name}.",
        ]);

        return redirect()
            ->route('admin.onboarding.invites.show', $invite)
            ->with('success', 'Onboarding invite created.');
    }

    public function show(OnboardingInvite $invite)
    {
        $invite->load([
            'template',
            'submission',
            'missingInfoItems.followUps',
            'missingInfoFollowUps.missingInfoItem',
            'reviewChecklistItems',
            'documentRequirements',
            'notes' => fn ($query) => $query->latest(),
            'activityLogs' => fn ($query) => $query->latest(),
        ]);

        $publicUrl = route('public.onboarding.show', $invite->token);

        return view('admin.onboarding.invites.show', [
            'invite' => $invite,
            'publicUrl' => $publicUrl,
            'statuses' => OnboardingInvite::statuses(),
            'documentRequirementStatuses' => DocumentRequirement::statuses(),
        ]);
    }

    public function updateStatus(Request $request, OnboardingInvite $invite)
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:sent,started,submitted,in_review,needs_info,approved,rejected,expired'],
        ]);

        $oldStatus = $invite->status;

        $invite->update([
            'status' => $validated['status'],
        ]);

        ActivityLog::create([
            'onboarding_invite_id' => $invite->id,
            'actor_name' => 'Admin User',
            'action' => 'status_changed',
            'description' => "Status changed from {$oldStatus} to {$validated['status']}.",
        ]);

        return redirect()
            ->route('admin.onboarding.invites.show', $invite)
            ->with('success', 'Onboarding status updated.');
    }

    public function toggleReviewChecklistItem(OnboardingInvite $invite, ReviewChecklistItem $item)
    {
        if ((int) $item->onboarding_invite_id !== (int) $invite->id) {
            abort(404);
        }

        $wasCompleted = $item->is_completed;

        $item->update([
            'is_completed' => ! $wasCompleted,
            'completed_at' => $wasCompleted ? null : now(),
            'completed_by' => $wasCompleted ? null : 'Admin User',
        ]);

        ActivityLog::create([
            'onboarding_invite_id' => $invite->id,
            'actor_name' => 'Admin User',
            'action' => 'review_checklist_updated',
            'description' => $wasCompleted
                ? "Marked checklist item as incomplete: \"{$item->label}\"."
                : "Marked checklist item as complete: \"{$item->label}\".",
        ]);

        return redirect()
            ->route('admin.onboarding.invites.show', $invite)
            ->with('success', 'Review checklist updated.');
    }

    public function storeNote(Request $request, OnboardingInvite $invite)
    {
        $validated = $request->validate([
            'note' => ['required', 'string', 'max:3000'],
        ]);

        OnboardingNote::create([
            'onboarding_invite_id' => $invite->id,
            'author_name' => 'Admin User',
            'note' => $validated['note'],
        ]);

        ActivityLog::create([
            'onboarding_invite_id' => $invite->id,
            'actor_name' => 'Admin User',
            'action' => 'note_added',
            'description' => 'Admin note added.',
        ]);

        return redirect()
            ->route('admin.onboarding.invites.show', $invite)
            ->with('success', 'Note added.');
    }

    public function activityLog()
    {
        $activityLogs = ActivityLog::with('invite')
            ->latest()
            ->paginate(20);

        return view('admin.onboarding.activity-log.index', [
            'activityLogs' => $activityLogs,
        ]);
    }

    public function dashboard()
    {
        $totalInvites = OnboardingInvite::count();
        $submitted = OnboardingInvite::whereNotNull('submitted_at')->count();
        $inReview = OnboardingInvite::where('status', 'in_review')->count();
        $needsInfo = OnboardingInvite::where('status', 'needs_info')->count();
        $approved = OnboardingInvite::where('status', 'approved')->count();

        $recentInvites = OnboardingInvite::with(['template'])
            ->withCount([
                'unresolvedMissingInfoItems',
            ])
            ->latest()
            ->take(8)
            ->get();

        $reviewQueue = OnboardingInvite::with(['template', 'submission'])
            ->whereIn('status', ['submitted', 'in_review', 'needs_info'])
            ->latest()
            ->take(6)
            ->get();

        $missingInfoItems = MissingInfoItem::with(['invite', 'submission'])
            ->where('resolved', false)
            ->latest()
            ->take(8)
            ->get();

        $recentActivity = ActivityLog::with('invite')
            ->latest()
            ->take(8)
            ->get();

        $completionPercent = $totalInvites > 0
            ? round(($approved / $totalInvites) * 100)
            : 0;

        return view('admin.onboarding.dashboard', [
            'totalInvites' => $totalInvites,
            'submitted' => $submitted,
            'inReview' => $inReview,
            'needsInfo' => $needsInfo,
            'approved' => $approved,
            'recentInvites' => $recentInvites,
            'reviewQueue' => $reviewQueue,
            'missingInfoItems' => $missingInfoItems,
            'recentActivity' => $recentActivity,
            'completionPercent' => $completionPercent,
        ]);
    }

    public function reports()
    {
        $totalInvites = OnboardingInvite::count();

        $statusCounts = [
            'sent' => OnboardingInvite::where('status', 'sent')->count(),
            'started' => OnboardingInvite::where('status', 'started')->count(),
            'submitted' => OnboardingInvite::where('status', 'submitted')->count(),
            'in_review' => OnboardingInvite::where('status', 'in_review')->count(),
            'needs_info' => OnboardingInvite::where('status', 'needs_info')->count(),
            'approved' => OnboardingInvite::where('status', 'approved')->count(),
            'rejected' => OnboardingInvite::where('status', 'rejected')->count(),
            'expired' => OnboardingInvite::where('status', 'expired')->count(),
        ];

        $submittedInvites = OnboardingInvite::whereNotNull('submitted_at')->count();
        $approvedInvites = $statusCounts['approved'];
        $needsInfoInvites = $statusCounts['needs_info'];

        $submissionRate = $totalInvites > 0
            ? round(($submittedInvites / $totalInvites) * 100)
            : 0;

        $approvalRate = $totalInvites > 0
            ? round(($approvedInvites / $totalInvites) * 100)
            : 0;

        $templates = OnboardingTemplate::withCount('invites')
            ->orderByDesc('invites_count')
            ->orderBy('name')
            ->get();

        $totalMissingInfoItems = MissingInfoItem::count();
        $resolvedMissingInfoItems = MissingInfoItem::where('resolved', true)->count();
        $openMissingInfoItems = MissingInfoItem::where('resolved', false)->count();

        $missingInfoResolutionRate = $totalMissingInfoItems > 0
            ? round(($resolvedMissingInfoItems / $totalMissingInfoItems) * 100)
            : 0;

        $totalChecklistItems = ReviewChecklistItem::count();
        $completedChecklistItems = ReviewChecklistItem::where('is_completed', true)->count();

        $checklistCompletionRate = $totalChecklistItems > 0
            ? round(($completedChecklistItems / $totalChecklistItems) * 100)
            : 0;

        $documentRequirementCounts = [
            'missing' => DocumentRequirement::where('status', 'missing')->count(),
            'provided' => DocumentRequirement::where('status', 'provided')->count(),
            'reviewed' => DocumentRequirement::where('status', 'reviewed')->count(),
            'not_required' => DocumentRequirement::where('status', 'not_required')->count(),
        ];

        $totalDocumentRequirements = array_sum($documentRequirementCounts);

        $documentReviewRate = $totalDocumentRequirements > 0
            ? round(($documentRequirementCounts['reviewed'] / $totalDocumentRequirements) * 100)
            : 0;

        $followUpSummary = [
            'open' => class_exists(MissingInfoFollowUp::class)
                ? MissingInfoFollowUp::where('status', 'open')->count()
                : 0,
            'resolved' => class_exists(MissingInfoFollowUp::class)
                ? MissingInfoFollowUp::where('status', 'resolved')->count()
                : 0,
            'cancelled' => class_exists(MissingInfoFollowUp::class)
                ? MissingInfoFollowUp::where('status', 'cancelled')->count()
                : 0,
        ];

        $recentNeedsInfoInvites = OnboardingInvite::with(['template', 'submission'])
            ->withCount([
                'unresolvedMissingInfoItems',
            ])
            ->where('status', 'needs_info')
            ->latest()
            ->take(8)
            ->get();

        $recentActivity = ActivityLog::with('invite')
            ->latest()
            ->take(8)
            ->get();

        return view('admin.onboarding.reports.index', [
            'totalInvites' => $totalInvites,
            'statusCounts' => $statusCounts,
            'submittedInvites' => $submittedInvites,
            'approvedInvites' => $approvedInvites,
            'needsInfoInvites' => $needsInfoInvites,
            'submissionRate' => $submissionRate,
            'approvalRate' => $approvalRate,
            'templates' => $templates,
            'totalMissingInfoItems' => $totalMissingInfoItems,
            'resolvedMissingInfoItems' => $resolvedMissingInfoItems,
            'openMissingInfoItems' => $openMissingInfoItems,
            'missingInfoResolutionRate' => $missingInfoResolutionRate,
            'totalChecklistItems' => $totalChecklistItems,
            'completedChecklistItems' => $completedChecklistItems,
            'checklistCompletionRate' => $checklistCompletionRate,
            'documentRequirementCounts' => $documentRequirementCounts,
            'totalDocumentRequirements' => $totalDocumentRequirements,
            'documentReviewRate' => $documentReviewRate,
            'followUpSummary' => $followUpSummary,
            'recentNeedsInfoInvites' => $recentNeedsInfoInvites,
            'recentActivity' => $recentActivity,
        ]);
    }

    public function exportSubmissionsCsv()
    {
        $fileName = 'onboarding-submissions-' . now()->format('Y-m-d-His') . '.csv';

        ActivityLog::create([
            'actor_name' => 'Admin User',
            'action' => 'csv_exported',
            'description' => 'Onboarding submissions CSV exported.',
        ]);

        $invites = OnboardingInvite::with([
            'template',
            'submission',
            'missingInfoItems',
        ])
            ->latest()
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        $callback = function () use ($invites) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Invite ID',
                'Template',
                'Recipient Name',
                'Recipient Email',
                'Invite Organisation',
                'Invite Role',
                'Status',
                'Missing Info Count',
                'Submitted At',
                'Submission First Name',
                'Submission Last Name',
                'Submission Email',
                'Submission Phone',
                'Submission Organisation',
                'Submission Role',
                'Emergency Contact Name',
                'Emergency Contact Phone',
                'Applicant Notes',
                'Invite Created At',
            ]);

            foreach ($invites as $invite) {
                $submission = $invite->submission;

                fputcsv($handle, [
                    $invite->id,
                    $invite->template?->name,
                    $invite->recipient_name,
                    $invite->recipient_email,
                    $invite->organisation,
                    $invite->role,
                    $invite->statusLabel(),
                    $invite->missingInfoItems->where('resolved', false)->count(),
                    optional($invite->submitted_at)->format('Y-m-d H:i:s'),
                    $submission?->first_name,
                    $submission?->last_name,
                    $submission?->email,
                    $submission?->phone,
                    $submission?->organisation,
                    $submission?->role,
                    $submission?->emergency_contact_name,
                    $submission?->emergency_contact_phone,
                    $submission?->notes,
                    optional($invite->created_at)->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }

    private function createReviewChecklistItemsFromTemplate(OnboardingInvite $invite): void
    {
        $invite->loadMissing('template');

        if (! $invite->template || empty($invite->template->review_checklist)) {
            return;
        }

        foreach ($invite->template->review_checklist as $index => $checklistItem) {
            $label = is_array($checklistItem)
                ? ($checklistItem['label'] ?? $checklistItem['name'] ?? null)
                : $checklistItem;

            if (! $label || ! is_string($label)) {
                continue;
            }

            ReviewChecklistItem::create([
                'onboarding_invite_id' => $invite->id,
                'label' => trim($label),
                'description' => is_array($checklistItem)
                    ? ($checklistItem['description'] ?? null)
                    : null,
                'sort_order' => $index + 1,
                'is_completed' => false,
            ]);
        }
    }

    private function createDocumentRequirementsFromTemplate(OnboardingInvite $invite): void
    {
        $invite->loadMissing('template');

        if (! $invite->template || empty($invite->template->required_documents)) {
            return;
        }

        foreach ($invite->template->required_documents as $index => $documentRequirement) {
            $label = is_array($documentRequirement)
                ? ($documentRequirement['label'] ?? $documentRequirement['name'] ?? null)
                : $documentRequirement;

            if (! $label || ! is_string($label)) {
                continue;
            }

            DocumentRequirement::create([
                'onboarding_invite_id' => $invite->id,
                'label' => trim($label),
                'description' => is_array($documentRequirement)
                    ? ($documentRequirement['description'] ?? null)
                    : null,
                'status' => 'missing',
                'sort_order' => $index + 1,
            ]);
        }
    }

    public function updateDocumentRequirementStatus(Request $request, OnboardingInvite $invite, DocumentRequirement $requirement)
    {
        if ((int) $requirement->onboarding_invite_id !== (int) $invite->id) {
            abort(404);
        }

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:missing,provided,reviewed,not_required'],
        ]);

        $oldStatus = $requirement->status;
        $newStatus = $validated['status'];

        $requirement->update([
            'status' => $newStatus,
            'reviewed_at' => $newStatus === 'reviewed' ? now() : $requirement->reviewed_at,
            'reviewed_by' => $newStatus === 'reviewed' ? 'Admin User' : $requirement->reviewed_by,
        ]);

        ActivityLog::create([
            'onboarding_invite_id' => $invite->id,
            'actor_name' => 'Admin User',
            'action' => 'document_requirement_updated',
            'description' => "Document requirement \"{$requirement->label}\" changed from {$oldStatus} to {$newStatus}.",
        ]);

        return redirect()
            ->route('admin.onboarding.invites.show', $invite)
            ->with('success', 'Document requirement updated.');
    }

    public function storeMissingInfoFollowUp(Request $request, OnboardingInvite $invite, MissingInfoItem $item)
    {
        if ((int) $item->onboarding_invite_id !== (int) $invite->id) {
            abort(404);
        }

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:3000'],
            'due_at' => ['nullable', 'date'],
        ]);

        MissingInfoFollowUp::create([
            'onboarding_invite_id' => $invite->id,
            'missing_info_item_id' => $item->id,
            'message' => $validated['message'],
            'status' => 'open',
            'requested_by' => 'Admin User',
            'requested_at' => now(),
            'due_at' => $validated['due_at'] ?? null,
        ]);

        ActivityLog::create([
            'onboarding_invite_id' => $invite->id,
            'actor_name' => 'Admin User',
            'action' => 'missing_info_follow_up_created',
            'description' => "Follow-up requested for missing info item \"{$item->label}\".",
        ]);

        return redirect()
            ->route('admin.onboarding.invites.show', $invite)
            ->with('success', 'Missing info follow-up created.');
    }

    public function resolveMissingInfoItem(OnboardingInvite $invite, MissingInfoItem $item)
    {
        if ((int) $item->onboarding_invite_id !== (int) $invite->id) {
            abort(404);
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
                'resolved_by' => 'Admin User',
                'resolved_at' => now(),
            ]);

        $remainingMissingInfoCount = MissingInfoItem::where('onboarding_invite_id', $invite->id)
            ->where('resolved', false)
            ->count();

        if ($remainingMissingInfoCount === 0 && $invite->status === 'needs_info') {
            $invite->update([
                'status' => 'in_review',
            ]);
        }

        ActivityLog::create([
            'onboarding_invite_id' => $invite->id,
            'actor_name' => 'Admin User',
            'action' => 'missing_info_resolved',
            'description' => "Missing info item resolved: \"{$item->label}\".",
        ]);

        return redirect()
            ->route('admin.onboarding.invites.show', $invite)
            ->with('success', 'Missing info item marked as resolved.');
    }

    public function previewInviteEmail(OnboardingInvite $invite)
    {
        $invite->loadMissing('template');

        return view('emails.onboarding.invite', [
            'invite' => $invite,
            'publicUrl' => route('public.onboarding.show', $invite->token),
        ]);
    }

    public function sendInviteEmail(OnboardingInvite $invite, MicrosoftGraphMailService $mailService)
    {
        $invite->loadMissing('template');

        $sendCountBeforeSend = (int) ($invite->email_send_count ?? 0);
        $provider = config('onboarding.email_provider', 'microsoft_graph');

        try {
            $htmlBody = view('emails.onboarding.invite', [
                'invite' => $invite,
                'publicUrl' => route('public.onboarding.show', $invite->token),
            ])->render();

            if ($provider !== 'microsoft_graph') {
                throw new \RuntimeException("Unsupported email provider: {$provider}");
            }

            $mailService->sendInviteEmail($invite, $htmlBody);

            $invite->update([
                'email_last_sent_at' => now(),
                'email_send_count' => $sendCountBeforeSend + 1,
                'email_provider' => 'microsoft_graph',
                'email_last_error' => null,
            ]);

            ActivityLog::create([
                'onboarding_invite_id' => $invite->id,
                'actor_name' => 'Admin User',
                'action' => 'invite_email_sent',
                'description' => $sendCountBeforeSend > 0
                    ? "Onboarding invite email resent to {$invite->recipient_email} using Microsoft Graph."
                    : "Onboarding invite email sent to {$invite->recipient_email} using Microsoft Graph.",
            ]);

            return redirect()
                ->route('admin.onboarding.invites.show', $invite)
                ->with('success', 'Invite email sent through Microsoft Graph.');
        } catch (Throwable $exception) {
            $safeError = str($exception->getMessage())->limit(1500)->toString();

            $invite->update([
                'email_provider' => 'microsoft_graph',
                'email_last_error' => $safeError,
            ]);

            ActivityLog::create([
                'onboarding_invite_id' => $invite->id,
                'actor_name' => 'Admin User',
                'action' => 'invite_email_failed',
                'description' => "Onboarding invite email failed for {$invite->recipient_email}.",
            ]);

            return redirect()
                ->route('admin.onboarding.invites.show', $invite)
                ->with('error', 'Invite email failed. Check the Invite Email error details.');
        }
    }
}