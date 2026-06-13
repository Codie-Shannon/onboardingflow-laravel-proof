@if ($invite->submission)
    <h3>Submitted Details</h3>

    <p><strong>Name:</strong> {{ $invite->submission->first_name }} {{ $invite->submission->last_name }}</p>
    <p><strong>Email:</strong> {{ $invite->submission->email }}</p>
    <p><strong>Phone:</strong> {{ $invite->submission->phone ?? '-' }}</p>
    <p><strong>Organisation:</strong> {{ $invite->submission->organisation ?? '-' }}</p>
    <p><strong>Role:</strong> {{ $invite->submission->role ?? '-' }}</p>
    <p><strong>Emergency Contact:</strong> {{ $invite->submission->emergency_contact_name ?? '-' }} / {{ $invite->submission->emergency_contact_phone ?? '-' }}</p>

    @if ($invite->submission->notes)
        <p><strong>Notes:</strong> {{ $invite->submission->notes }}</p>
    @endif
@endif