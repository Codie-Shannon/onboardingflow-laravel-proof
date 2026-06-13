<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\MissingInfoItem;
use App\Models\OnboardingInvite;
use App\Models\OnboardingNote;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OnboardingInviteController extends Controller
{
    public function index()
    {
        $invites = OnboardingInvite::withCount([
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

        ActivityLog::create([
            'onboarding_invite_id' => $invite->id,
            'actor_name' => 'Admin User',
            'action' => 'invite_created',
            'description' => "Onboarding invite created for {$invite->recipient_name}.",
        ]);

        return redirect()
            ->route('admin.onboarding.invites.show', $invite)
            ->with('success', 'Onboarding invite created.');
    }

    public function show(OnboardingInvite $invite)
    {
        $invite->load([
            'submission',
            'missingInfoItems',
            'notes' => fn ($query) => $query->latest(),
            'activityLogs' => fn ($query) => $query->latest(),
        ]);
    
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

        $recentInvites = OnboardingInvite::withCount([
            'unresolvedMissingInfoItems',
        ])
            ->latest()
            ->take(8)
            ->get();

        $reviewQueue = OnboardingInvite::with(['submission'])
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

        return response()->stream($callback, 200, $headers);
    }
}