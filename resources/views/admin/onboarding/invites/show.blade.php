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
</body>
</html>