<!DOCTYPE html>
<html>
<head>
    <title>Activity Log</title>
</head>
<body>
    <h1>Activity Log</h1>

    <p>
        <a href="{{ route('admin.onboarding.dashboard') }}">Dashboard</a> |
        <a href="{{ route('admin.onboarding.invites.index') }}">View invites</a>
    </p>

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
            @forelse ($activityLogs as $activity)
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

    {{ $activityLogs->links() }}
</body>
</html>