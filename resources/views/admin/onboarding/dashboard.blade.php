<!DOCTYPE html>
<html>
<head>
    <title>Onboarding Overview</title>
</head>
<body>
    <h1>Onboarding Overview</h1>

    <p>
        Replaces a manual PDF-email onboarding process with a trackable web workflow.
    </p>

    <p>
        <a href="{{ route('admin.onboarding.invites.create') }}">Create Invite</a> |
        <a href="{{ route('admin.onboarding.invites.index') }}">View Invites</a> |
        <a href="{{ route('admin.onboarding.activity-log.index') }}">Activity Log</a>
    </p>

    <h2>Metrics</h2>

    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>Total Invites</th>
                <th>Submitted</th>
                <th>In Review</th>
                <th>Needs Info</th>
                <th>Approved</th>
                <th>Completion</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $totalInvites }}</td>
                <td>{{ $submitted }}</td>
                <td>{{ $inReview }}</td>
                <td>{{ $needsInfo }}</td>
                <td>{{ $approved }}</td>
                <td>{{ $completionPercent }}%</td>
            </tr>
        </tbody>
    </table>

    <h2>Recent Onboarding Invites</h2>

    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Organisation</th>
                <th>Role</th>
                <th>Status</th>
                <th>Missing Info</th>
                <th>Created</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($recentInvites as $invite)
                <tr>
                    <td>{{ $invite->recipient_name }}</td>
                    <td>{{ $invite->recipient_email }}</td>
                    <td>{{ $invite->organisation ?? '-' }}</td>
                    <td>{{ $invite->role ?? '-' }}</td>
                    <td>{{ $invite->statusLabel() }}</td>
                    <td>{{ $invite->unresolved_missing_info_items_count ?? 0 }}</td>
                    <td>{{ $invite->created_at->format('d M Y H:i') }}</td>
                    <td>
                        <a href="{{ route('admin.onboarding.invites.show', $invite) }}">View</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">No invites yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h2>Review Queue</h2>

    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>Name</th>
                <th>Status</th>
                <th>Submitted</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($reviewQueue as $invite)
                <tr>
                    <td>{{ $invite->recipient_name }}</td>
                    <td>{{ $invite->statusLabel() }}</td>
                    <td>{{ optional($invite->submitted_at)->format('d M Y H:i') ?? '-' }}</td>
                    <td>
                        <a href="{{ route('admin.onboarding.invites.show', $invite) }}">Review</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">Nothing waiting for review.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h2>Missing Information Alerts</h2>

    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>Applicant</th>
                <th>Missing Item</th>
                <th>Severity</th>
                <th>Created</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($missingInfoItems as $item)
                <tr>
                    <td>{{ $item->invite?->recipient_name ?? '-' }}</td>
                    <td>{{ $item->label }}</td>
                    <td>{{ ucfirst($item->severity) }}</td>
                    <td>{{ $item->created_at->format('d M Y H:i') }}</td>
                    <td>
                        @if ($item->invite)
                            <a href="{{ route('admin.onboarding.invites.show', $item->invite) }}">View</a>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No missing information alerts.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h2>Recent Activity</h2>

    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>Created</th>
                <th>Actor</th>
                <th>Action</th>
                <th>Description</th>
                <th>Invite</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($recentActivity as $activity)
                <tr>
                    <td>{{ $activity->created_at->format('d M Y H:i') }}</td>
                    <td>{{ $activity->actor_name }}</td>
                    <td>{{ ucwords(str_replace('_', ' ', $activity->action)) }}</td>
                    <td>{{ $activity->description ?? '-' }}</td>
                    <td>
                        @if ($activity->invite)
                            <a href="{{ route('admin.onboarding.invites.show', $activity->invite) }}">
                                {{ $activity->invite->recipient_name }}
                            </a>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No activity yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>