<!DOCTYPE html>
<html>
<head>
    <title>Create Onboarding Invite</title>
</head>
<body>
    <h1>Create Onboarding Invite</h1>

    <p>
        <a href="{{ route('admin.onboarding.invites.index') }}">Back to invites</a>
    </p>

    @if ($errors->any())
        <div style="color: red;">
            <strong>Please fix the following:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.onboarding.invites.store') }}">
        @csrf

        <p>
            <label>Recipient Name</label><br>
            <input type="text" name="recipient_name" value="{{ old('recipient_name') }}" required>
        </p>

        <p>
            <label>Recipient Email</label><br>
            <input type="email" name="recipient_email" value="{{ old('recipient_email') }}" required>
        </p>

        <p>
            <label>Role / Position</label><br>
            <input type="text" name="role" value="{{ old('role') }}">
        </p>

        <p>
            <label>Organisation</label><br>
            <input type="text" name="organisation" value="{{ old('organisation') }}">
        </p>

        <p>
            <label>Message</label><br>
            <textarea name="message" rows="4">{{ old('message') }}</textarea>
        </p>

        <button type="submit">Create Invite</button>
    </form>
</body>
</html>