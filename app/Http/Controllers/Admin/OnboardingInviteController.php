<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OnboardingInvite;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\ActivityLog;
use App\Models\OnboardingNote;

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
}