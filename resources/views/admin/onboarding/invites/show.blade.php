<!DOCTYPE html>
<html>
<head>
    <title>Onboarding Invite</title>
</head>
<body>
    <h1>Onboarding Invite</h1>

    <p>
        <a href="{{ route('admin.onboarding.invites.index') }}">Back to invites</a>
    </p>

    @if (session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    <h2>{{ $invite->recipient_name }}</h2>

    <p><strong>Email:</strong> {{ $invite->recipient_email }}</p>
    <p><strong>Organisation:</strong> {{ $invite->organisation ?? '-' }}</p>
    <p><strong>Role:</strong> {{ $invite->role ?? '-' }}</p>
    <p><strong>Status:</strong> {{ $invite->statusLabel() }}</p>

    <h3>Review Status</h3>

    <form method="POST" action="{{ route('admin.onboarding.invites.update-status', $invite) }}">
        @csrf

        <label>Status</label><br>
        <select name="status">
            @foreach ($statuses as $value => $label)
                <option value="{{ $value }}" @selected($invite->status === $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>

        <button type="submit">Update Status</button>
    </form>

    @if ($invite->missingInfoItems->count() > 0)
        <h3>Missing Information</h3>

        <table border="1" cellpadding="8">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Description</th>
                    <th>Severity</th>
                    <th>Resolved</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invite->missingInfoItems as $item)
                    <tr>
                        <td>{{ $item->label }}</td>
                        <td>{{ $item->description }}</td>
                        <td>{{ ucfirst($item->severity) }}</td>
                        <td>{{ $item->resolved ? 'Yes' : 'No' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if ($invite->submission)
        <h3>Submitted Details</h3>

        <p><strong>Name:</strong> {{ $invite->submission->first_name }} {{ $invite->submission->last_name }}</p>
        <p><strong>Email:</strong> {{ $invite->submission->email }}</p>
        <p><strong>Phone:</strong> {{ $invite->submission->phone ?? '-' }}</p>
        <p><strong>Organisation:</strong> {{ $invite->submission->organisation ?? '-' }}</p>
        <p><strong>Role:</strong> {{ $invite->submission->role ?? '-' }}</p>
        <p><strong>Emergency Contact:</strong>
            {{ $invite->submission->emergency_contact_name ?? '-' }}
            /
            {{ $invite->submission->emergency_contact_phone ?? '-' }}
        </p>

        @if ($invite->submission->notes)
            <p><strong>Notes:</strong> {{ $invite->submission->notes }}</p>
        @endif
    @endif

    <p><strong>Expires:</strong> {{ optional($invite->expires_at)->format('d M Y H:i') ?? '-' }}</p>

    <h3>Public Onboarding Link</h3>

    <input type="text" value="{{ $publicUrl }}" style="width: 600px;" readonly>

    <p>
        <a href="{{ $publicUrl }}" target="_blank">Open public onboarding form</a>
    </p>

    @if ($invite->message)
        <h3>Message</h3>
        <p>{{ $invite->message }}</p>
    @endif

    <h3>Admin Notes</h3>

    <form method="POST" action="{{ route('admin.onboarding.invites.notes.store', $invite) }}">
        @csrf

        <p>
            <label>Add Note</label><br>
            <textarea name="note" rows="4" style="width: 600px;" required>{{ old('note') }}</textarea>
        </p>

        <button type="submit">Add Note</button>
    </form>

    @if ($invite->notes->count() > 0)
        <table border="1" cellpadding="8">
            <thead>
                <tr>
                    <th>Author</th>
                    <th>Note</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invite->notes as $note)
                    <tr>
                        <td>{{ $note->author_name }}</td>
                        <td>{{ $note->note }}</td>
                        <td>{{ $note->created_at->format('d M Y H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No notes yet.</p>
    @endif

    <h3>Activity Log</h3>

    @if ($invite->activityLogs->count() > 0)
        <table border="1" cellpadding="8">
            <thead>
                <tr>
                    <th>Actor</th>
                    <th>Action</th>
                    <th>Description</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invite->activityLogs as $activity)
                    <tr>
                        <td>{{ $activity->actor_name }}</td>
                        <td>{{ ucwords(str_replace('_', ' ', $activity->action)) }}</td>
                        <td>{{ $activity->description ?? '-' }}</td>
                        <td>{{ $activity->created_at->format('d M Y H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No activity yet.</p>
    @endif
</body>
</html>