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
                ? "Onboarding invite created for {$invite->recipient_name} using {$template->name} template."
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
            'missingInfoItems',
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
}