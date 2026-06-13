<x-layouts.admin
    title="Onboarding Invite"
    heading="Onboarding Invite"
    subheading="Review submitted onboarding details, missing information, notes, and activity.">

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <section class="xl:col-span-2 space-y-6">
            <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-900">{{ $invite->recipient_name }}</h2>
                        <p class="text-sm text-slate-500 mt-1">{{ $invite->recipient_email }}</p>
                    </div>

                    <span class="rounded-full px-3 py-1 text-xs font-semibold
                        {{ $invite->status === 'approved' ? 'bg-green-50 text-green-700' : '' }}
                        {{ $invite->status === 'needs_info' ? 'bg-orange-50 text-orange-700' : '' }}
                        {{ $invite->status === 'in_review' ? 'bg-amber-50 text-amber-700' : '' }}
                        {{ $invite->status === 'submitted' ? 'bg-blue-50 text-blue-700' : '' }}
                        {{ in_array($invite->status, ['sent', 'started']) ? 'bg-teal-50 text-teal-700' : '' }}
                        {{ in_array($invite->status, ['rejected', 'expired']) ? 'bg-red-50 text-red-700' : '' }}">
                        {{ $invite->statusLabel() }}
                    </span>
                </div>

                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <div class="text-slate-500">Template</div>
                        <div class="font-medium text-slate-900">
                            @if ($invite->template)
                                <a href="{{ route('admin.onboarding.templates.show', $invite->template) }}"
                                   class="text-teal-800 hover:text-teal-950">
                                    {{ $invite->template->name }}
                                </a>
                            @else
                                -
                            @endif
                        </div>
                    </div>

                    <div>
                        <div class="text-slate-500">Organisation</div>
                        <div class="font-medium text-slate-900">{{ $invite->organisation ?? '-' }}</div>
                    </div>

                    <div>
                        <div class="text-slate-500">Role</div>
                        <div class="font-medium text-slate-900">{{ $invite->role ?? '-' }}</div>
                    </div>

                    <div>
                        <div class="text-slate-500">Expires</div>
                        <div class="font-medium text-slate-900">{{ optional($invite->expires_at)->format('d M Y H:i') ?? '-' }}</div>
                    </div>

                    <div>
                        <div class="text-slate-500">Submitted</div>
                        <div class="font-medium text-slate-900">{{ optional($invite->submitted_at)->format('d M Y H:i') ?? '-' }}</div>
                    </div>
                </div>

                @if ($invite->message)
                    <div class="mt-6 rounded-xl bg-slate-50 border border-slate-200 p-4">
                        <div class="text-sm font-semibold text-slate-900">Invite Message</div>
                        <p class="mt-2 text-sm text-slate-600">{{ $invite->message }}</p>
                    </div>
                @endif
            </div>

            @if ($invite->submission)
                <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-slate-900 mb-4">Submitted Details</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <div class="text-slate-500">Name</div>
                            <div class="font-medium text-slate-900">{{ $invite->submission->first_name }} {{ $invite->submission->last_name }}</div>
                        </div>

                        <div>
                            <div class="text-slate-500">Email</div>
                            <div class="font-medium text-slate-900">{{ $invite->submission->email }}</div>
                        </div>

                        <div>
                            <div class="text-slate-500">Phone</div>
                            <div class="font-medium text-slate-900">{{ $invite->submission->phone ?? '-' }}</div>
                        </div>

                        <div>
                            <div class="text-slate-500">Organisation</div>
                            <div class="font-medium text-slate-900">{{ $invite->submission->organisation ?? '-' }}</div>
                        </div>

                        <div>
                            <div class="text-slate-500">Role</div>
                            <div class="font-medium text-slate-900">{{ $invite->submission->role ?? '-' }}</div>
                        </div>

                        <div>
                            <div class="text-slate-500">Emergency Contact</div>
                            <div class="font-medium text-slate-900">
                                {{ $invite->submission->emergency_contact_name ?? '-' }}
                                /
                                {{ $invite->submission->emergency_contact_phone ?? '-' }}
                            </div>
                        </div>
                    </div>

                    @if ($invite->submission->notes)
                        <div class="mt-5 rounded-xl bg-slate-50 border border-slate-200 p-4">
                            <div class="text-sm font-semibold text-slate-900">Applicant Notes</div>
                            <p class="mt-2 text-sm text-slate-600">{{ $invite->submission->notes }}</p>
                        </div>
                    @endif
                </div>
            @endif

            @if ($invite->missingInfoItems->count() > 0)
                <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-200">
                        <h2 class="text-lg font-semibold text-slate-900">Missing Information</h2>
                    </div>

                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 text-slate-500">
                            <tr>
                                <th class="text-left px-6 py-3">Item</th>
                                <th class="text-left px-6 py-3">Description</th>
                                <th class="text-left px-6 py-3">Severity</th>
                                <th class="text-left px-6 py-3">Resolved</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @foreach ($invite->missingInfoItems as $item)
                                <tr>
                                    <td class="px-6 py-4 font-medium text-orange-800">{{ $item->label }}</td>
                                    <td class="px-6 py-4 text-slate-600">{{ $item->description }}</td>
                                    <td class="px-6 py-4">
                                        <span class="rounded-full bg-orange-50 px-3 py-1 text-xs font-semibold text-orange-700">
                                            {{ ucfirst($item->severity) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">{{ $item->resolved ? 'Yes' : 'No' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <aside class="space-y-6">
            <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
                <h2 class="text-lg font-semibold text-slate-900">Review Status</h2>

                <form method="POST" action="{{ route('admin.onboarding.invites.update-status', $invite) }}" class="mt-4 space-y-3">
                    @csrf

                    <select name="status"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
                        @foreach ($statuses as $value => $label)
                            <option value="{{ $value }}" @selected($invite->status === $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit"
                            class="w-full rounded-xl bg-teal-800 px-4 py-3 text-sm font-semibold text-white hover:bg-teal-900">
                        Update Status
                    </button>
                </form>
            </div>

            <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
                <h2 class="text-lg font-semibold text-slate-900">Public Onboarding Link</h2>

                <input type="text"
                       value="{{ $publicUrl }}"
                       readonly
                       class="mt-4 w-full rounded-xl border border-slate-300 px-4 py-3 text-xs text-slate-600">

                <a href="{{ $publicUrl }}"
                   target="_blank"
                   class="mt-3 inline-flex w-full justify-center rounded-xl border border-teal-700 px-4 py-3 text-sm font-semibold text-teal-800 hover:bg-teal-50">
                    Open Public Form
                </a>
            </div>

            <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
                <h2 class="text-lg font-semibold text-slate-900">Admin Notes</h2>

                <form method="POST" action="{{ route('admin.onboarding.invites.notes.store', $invite) }}" class="mt-4">
                    @csrf

                    <textarea name="note"
                              rows="4"
                              required
                              class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100"
                              placeholder="Add an internal note...">{{ old('note') }}</textarea>

                    <button type="submit"
                            class="mt-3 w-full rounded-xl bg-teal-800 px-4 py-3 text-sm font-semibold text-white hover:bg-teal-900">
                        Add Note
                    </button>
                </form>

                <div class="mt-5 space-y-3">
                    @forelse ($invite->notes as $note)
                        <div class="rounded-xl border border-slate-200 p-4">
                            <div class="text-sm font-medium text-slate-900">{{ $note->author_name }}</div>
                            <p class="mt-1 text-sm text-slate-600">{{ $note->note }}</p>
                            <div class="mt-2 text-xs text-slate-400">{{ $note->created_at->format('d M Y H:i') }}</div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No notes yet.</p>
                    @endforelse
                </div>
            </div>
        </aside>
    </div>

    <section class="mt-6 rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200">
            <h2 class="text-lg font-semibold text-slate-900">Activity Log</h2>
        </div>

        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500">
                <tr>
                    <th class="text-left px-6 py-3">Actor</th>
                    <th class="text-left px-6 py-3">Action</th>
                    <th class="text-left px-6 py-3">Description</th>
                    <th class="text-left px-6 py-3">Created</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100">
                @forelse ($invite->activityLogs as $activity)
                    <tr>
                        <td class="px-6 py-4">{{ $activity->actor_name }}</td>
                        <td class="px-6 py-4">
                            <span class="rounded-full bg-teal-50 px-3 py-1 text-xs font-semibold text-teal-800">
                                {{ ucwords(str_replace('_', ' ', $activity->action)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-slate-600">{{ $activity->description ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $activity->created_at->format('d M Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-slate-500">No activity yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>

</x-layouts.admin>