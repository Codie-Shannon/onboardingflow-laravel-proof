<!DOCTYPE html>
<html>
<head>
    <title>Onboarding Invites</title>
</head>
<body>
    <h1>Onboarding Invites</h1>

    @if (session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    <p>
        <a href="{{ route('admin.onboarding.dashboard') }}">Dashboard</a> |
        <a href="{{ route('admin.onboarding.invites.create') }}">Create Invite</a> |
        <a href="{{ route('admin.onboarding.activity-log.index') }}">Activity Log</a>
    </p>

    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Organisation</th>
                <th>Role</th>
                <th>Status</th>
                <th>Missing Info</th>
                <th>Expires</th>
                <th>Created</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($invites as $invite)
                <tr>
                    <td>{{ $invite->recipient_name }}</td>
                    <td>{{ $invite->recipient_email }}</td>
                    <td>{{ $invite->organisation ?? '-' }}</td>
                    <td>{{ $invite->role ?? '-' }}</td>
                    <td>{{ $invite->statusLabel() }}</td>
                    <td>{{ $invite->unresolved_missing_info_items_count }}</td>
                    <td>{{ optional($invite->expires_at)->format('d M Y') ?? '-' }}</td>
                    <td>{{ $invite->created_at->format('d M Y H:i') }}</td>
                    <td>
                        <a href="{{ route('admin.onboarding.invites.show', $invite) }}">View</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">No invites yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $invites->links() }}
</body>
</html>