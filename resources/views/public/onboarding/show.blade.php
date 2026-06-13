<!DOCTYPE html>
<html>
<head>
    <title>Complete Onboarding Form</title>
</head>
<body>
    <h1>Complete Onboarding Form</h1>

    <p>
        You have been invited to complete onboarding for:
        <strong>{{ $invite->organisation ?? 'your organisation' }}</strong>
    </p>

    @if ($invite->message)
        <h3>Message</h3>
        <p>{{ $invite->message }}</p>
    @endif

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

    <form method="POST" action="{{ route('public.onboarding.store', $invite->token) }}">
        @csrf

        <h2>Personal Details</h2>

        <p>
            <label>First Name</label><br>
            <input type="text" name="first_name" value="{{ old('first_name') }}" required>
        </p>

        <p>
            <label>Last Name</label><br>
            <input type="text" name="last_name" value="{{ old('last_name') }}" required>
        </p>

        <p>
            <label>Email</label><br>
            <input type="email" name="email" value="{{ old('email', $invite->recipient_email) }}" required>
        </p>

        <p>
            <label>Phone</label><br>
            <input type="text" name="phone" value="{{ old('phone') }}">
        </p>

        <h2>Work Details</h2>

        <p>
            <label>Organisation</label><br>
            <input type="text" name="organisation" value="{{ old('organisation', $invite->organisation) }}">
        </p>

        <p>
            <label>Role / Position</label><br>
            <input type="text" name="role" value="{{ old('role', $invite->role) }}">
        </p>

        <h2>Emergency Contact</h2>

        <p>
            <label>Emergency Contact Name</label><br>
            <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}">
        </p>

        <p>
            <label>Emergency Contact Phone</label><br>
            <input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}">
        </p>

        <h2>Additional Notes</h2>

        <p>
            <label>Notes</label><br>
            <textarea name="notes" rows="5">{{ old('notes') }}</textarea>
        </p>

        <button type="submit">Submit Onboarding Form</button>
    </form>
</body>
</html>