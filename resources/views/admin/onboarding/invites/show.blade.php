<x-layouts.admin>
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-slate-900">
                        {{ $invite->recipient_name }}
                    </h1>

                    <span class="rounded-full bg-slate-100 px-3 py-1 text-sm font-medium text-slate-700">
                        {{ $invite->statusLabel() }}
                    </span>
                </div>

                <p class="mt-1 text-sm text-slate-600">
                    {{ $invite->recipient_email }}
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a
                    href="{{ route('admin.onboarding.invites.index') }}"
                    class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50"
                >
                    Back to Invites
                </a>

                <a
                    href="{{ url('/onboarding/' . $invite->token) }}"
                    target="_blank"
                    class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700"
                >
                    Open Public Form
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Invite Details</h2>

            <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Template</div>
                    <div class="mt-1 text-sm text-slate-900">
                        @if ($invite->template)
                            <a
                                href="{{ route('admin.onboarding.templates.show', $invite->template) }}"
                                class="font-medium text-blue-700 hover:text-blue-800 hover:underline"
                            >
                                {{ $invite->template->name }}
                            </a>
                        @else
                            -
                        @endif
                    </div>
                </div>

                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Organisation</div>
                    <div class="mt-1 text-sm text-slate-900">{{ $invite->organisation ?? '-' }}</div>
                </div>

                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Role</div>
                    <div class="mt-1 text-sm text-slate-900">{{ $invite->role ?? '-' }}</div>
                </div>

                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Expires</div>
                    <div class="mt-1 text-sm text-slate-900">
                        {{ optional($invite->expires_at)->format('d M Y H:i') ?? '-' }}
                    </div>
                </div>

                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Submitted</div>
                    <div class="mt-1 text-sm text-slate-900">
                        {{ optional($invite->submitted_at)->format('d M Y H:i') ?? '-' }}
                    </div>
                </div>
            </div>

            @if ($invite->message)
                <div class="mt-5 rounded-xl bg-slate-50 p-4">
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Invite Message</div>
                    <p class="mt-2 text-sm text-slate-700">{{ $invite->message }}</p>
                </div>
            @endif
        </section>

        @if ($invite->submission)
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Submitted Details</h2>

                <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Name</div>
                        <div class="mt-1 text-sm text-slate-900">
                            {{ $invite->submission->first_name }} {{ $invite->submission->last_name }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Email</div>
                        <div class="mt-1 text-sm text-slate-900">{{ $invite->submission->email }}</div>
                    </div>

                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Phone</div>
                        <div class="mt-1 text-sm text-slate-900">{{ $invite->submission->phone ?? '-' }}</div>
                    </div>

                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Organisation</div>
                        <div class="mt-1 text-sm text-slate-900">{{ $invite->submission->organisation ?? '-' }}</div>
                    </div>

                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Role</div>
                        <div class="mt-1 text-sm text-slate-900">{{ $invite->submission->role ?? '-' }}</div>
                    </div>

                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Emergency Contact</div>
                        <div class="mt-1 text-sm text-slate-900">
                            {{ $invite->submission->emergency_contact_name ?? '-' }}
                            /
                            {{ $invite->submission->emergency_contact_phone ?? '-' }}
                        </div>
                    </div>
                </div>

                @if ($invite->submission->notes)
                    <div class="mt-5 rounded-xl bg-slate-50 p-4">
                        <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Applicant Notes</div>
                        <p class="mt-2 text-sm text-slate-700">{{ $invite->submission->notes }}</p>
                    </div>
                @endif
            </section>
        @endif

        @if ($invite->missingInfoItems->count() > 0)
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Missing Information</h2>

                <div class="mt-4 overflow-hidden rounded-xl border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-slate-700">Item</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-700">Description</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-700">Severity</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-700">Resolved</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @foreach ($invite->missingInfoItems as $item)
                                <tr>
                                    <td class="px-4 py-3 text-slate-900">{{ $item->label }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $item->description }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ ucfirst($item->severity) }}</td>
                                    <td class="px-4 py-3 text-slate-600">
                                        {{ $item->resolved ? 'Yes' : 'No' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endif

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Review Checklist</h2>
                    <p class="mt-1 text-sm text-slate-600">
                        Track review steps copied from the selected onboarding template.
                    </p>
                </div>

                @php
                    $totalChecklistItems = $invite->reviewChecklistItems->count();
                    $completedChecklistItems = $invite->reviewChecklistItems->where('is_completed', true)->count();
                @endphp

                <div class="inline-flex w-fit items-center rounded-full bg-slate-100 px-3 py-1 text-sm font-medium text-slate-700">
                    {{ $completedChecklistItems }} / {{ $totalChecklistItems }} complete
                </div>
            </div>

            @if ($invite->reviewChecklistItems->isEmpty())
                <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4 text-sm text-slate-600">
                    No review checklist items exist for this invite.

                    @if ($invite->template)
                        This template may not have had checklist items when the invite was created.
                    @else
                        Select a template when creating an invite to generate review checklist items.
                    @endif
                </div>
            @else
                <div class="space-y-3">
                    @foreach ($invite->reviewChecklistItems as $item)
                        <div class="flex items-start justify-between gap-4 rounded-xl border border-slate-200 p-4">
                            <div class="flex items-start gap-3">
                                <form method="POST" action="{{ route('admin.onboarding.invites.review-checklist.toggle', [$invite, $item]) }}">
                                    @csrf

                                    <button
                                        type="submit"
                                        class="mt-0.5 flex h-6 w-6 items-center justify-center rounded-md border text-sm font-bold transition
                                            {{ $item->is_completed
                                                ? 'border-emerald-500 bg-emerald-500 text-white'
                                                : 'border-slate-300 bg-white text-transparent hover:border-slate-500'
                                            }}"
                                        title="{{ $item->is_completed ? 'Mark incomplete' : 'Mark complete' }}"
                                    >
                                        ✓
                                    </button>
                                </form>

                                <div>
                                    <div class="font-medium {{ $item->is_completed ? 'text-slate-500 line-through' : 'text-slate-900' }}">
                                        {{ $item->label }}
                                    </div>

                                    @if ($item->description)
                                        <div class="mt-1 text-sm text-slate-600">
                                            {{ $item->description }}
                                        </div>
                                    @endif

                                    @if ($item->is_completed)
                                        <div class="mt-1 text-xs text-emerald-700">
                                            Completed
                                            @if ($item->completed_by)
                                                by {{ $item->completed_by }}
                                            @endif

                                            @if ($item->completed_at)
                                                on {{ $item->completed_at->format('d M Y, g:i A') }}
                                            @endif
                                        </div>
                                    @else
                                        <div class="mt-1 text-xs text-slate-500">
                                            Pending review
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div>
                                @if ($item->is_completed)
                                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-medium text-emerald-700">
                                        Complete
                                    </span>
                                @else
                                    <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-medium text-amber-700">
                                        Pending
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Review Status</h2>

            <form
                method="POST"
                action="{{ route('admin.onboarding.invites.update-status', $invite) }}"
                class="mt-4 flex flex-col gap-3 sm:flex-row"
            >
                @csrf

                <select
                    name="status"
                    class="rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                >
                    @foreach ($statuses as $value => $label)
                        <option value="{{ $value }}" @selected($invite->status === $value)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>

                <button
                    type="submit"
                    class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-slate-800"
                >
                    Update Status
                </button>
            </form>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Public Onboarding Link</h2>

            <div class="mt-4 rounded-xl bg-slate-50 p-4">
                <a
                    href="{{ url('/onboarding/' . $invite->token) }}"
                    target="_blank"
                    class="break-all text-sm font-medium text-blue-700 hover:text-blue-800 hover:underline"
                >
                    {{ url('/onboarding/' . $invite->token) }}
                </a>
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Admin Notes</h2>

            <form method="POST" action="{{ route('admin.onboarding.invites.notes.store', $invite) }}" class="mt-4 space-y-3">
                @csrf

                <textarea
                    name="note"
                    rows="3"
                    class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                    placeholder="Add an internal admin note..."
                >{{ old('note') }}</textarea>

                <button
                    type="submit"
                    class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700"
                >
                    Add Note
                </button>
            </form>

            <div class="mt-5 space-y-3">
                @forelse ($invite->notes as $note)
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                            <div class="text-sm font-semibold text-slate-900">{{ $note->author_name }}</div>
                            <div class="text-xs text-slate-500">{{ $note->created_at->format('d M Y H:i') }}</div>
                        </div>

                        <p class="mt-2 text-sm text-slate-700">{{ $note->note }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-600">No notes yet.</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Activity Log</h2>

            @if ($invite->activityLogs->isEmpty())
                <p class="mt-4 text-sm text-slate-600">No activity yet.</p>
            @else
                <div class="mt-4 overflow-hidden rounded-xl border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-slate-700">Actor</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-700">Action</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-700">Description</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-700">Created</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @foreach ($invite->activityLogs as $activity)
                                <tr>
                                    <td class="px-4 py-3 text-slate-900">{{ $activity->actor_name }}</td>
                                    <td class="px-4 py-3 text-slate-600">
                                        {{ ucwords(str_replace('_', ' ', $activity->action)) }}
                                    </td>
                                    <td class="px-4 py-3 text-slate-600">
                                        {{ $activity->description ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-slate-600">
                                        {{ $activity->created_at->format('d M Y H:i') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
</x-layouts.admin>