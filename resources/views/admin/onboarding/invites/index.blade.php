<x-layouts.admin
    title="Onboarding Invites"
    heading="Onboarding Invites"
    subheading="Manage onboarding invitations and track their current status.">

    @php
        $currentUser = auth()->user();
    @endphp

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-slate-900">Invite Register</h2>
            <p class="text-sm text-slate-500">All onboarding invites created in this local proof.</p>
        </div>

        <div class="flex gap-3">
            @if ($currentUser?->canReview())
                <a href="{{ route('admin.onboarding.exports.submissions') }}"
                   class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Export CSV
                </a>
            @endif

            @if ($currentUser?->isAdmin())
                <a href="{{ route('admin.onboarding.invites.create') }}"
                   class="rounded-xl bg-teal-800 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-900">
                    + Create Invite
                </a>
            @endif
        </div>
    </div>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500">
                <tr>
                    <th class="text-left px-6 py-3">Name</th>
                    <th class="text-left px-6 py-3">Email</th>
                    <th class="text-left px-6 py-3">Organisation</th>
                    <th class="text-left px-6 py-3">Role</th>
                    <th class="text-left px-6 py-3">Status</th>
                    <th class="text-left px-6 py-3">Missing Info</th>
                    <th class="text-left px-6 py-3">Expires</th>
                    <th class="text-right px-6 py-3"></th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100">
                @forelse ($invites as $invite)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4">
                            <div class="font-medium text-slate-900">{{ $invite->recipient_name }}</div>
                            <div class="text-xs text-slate-400">Invite #{{ $invite->id }}</div>
                        </td>

                        <td class="px-6 py-4 text-slate-600">
                            {{ $invite->recipient_email }}
                        </td>

                        <td class="px-6 py-4 text-slate-700">
                            {{ $invite->organisation ?? '-' }}
                        </td>

                        <td class="px-6 py-4 text-slate-700">
                            {{ $invite->role ?? '-' }}
                        </td>

                        <td class="px-6 py-4">
                            <span class="rounded-full px-3 py-1 text-xs font-semibold
                                {{ $invite->status === 'approved' ? 'bg-green-50 text-green-700' : '' }}
                                {{ $invite->status === 'needs_info' ? 'bg-orange-50 text-orange-700' : '' }}
                                {{ $invite->status === 'in_review' ? 'bg-amber-50 text-amber-700' : '' }}
                                {{ $invite->status === 'submitted' ? 'bg-blue-50 text-blue-700' : '' }}
                                {{ in_array($invite->status, ['sent', 'started']) ? 'bg-teal-50 text-teal-700' : '' }}
                                {{ in_array($invite->status, ['rejected', 'expired']) ? 'bg-red-50 text-red-700' : '' }}">
                                {{ $invite->statusLabel() }}
                            </span>
                        </td>

                        <td class="px-6 py-4">
                            @if (($invite->unresolved_missing_info_items_count ?? 0) > 0)
                                <span class="rounded-full bg-orange-50 px-3 py-1 text-xs font-semibold text-orange-700">
                                    {{ $invite->unresolved_missing_info_items_count }} item(s)
                                </span>
                            @else
                                <span class="rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-700">
                                    Clear
                                </span>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-slate-600">
                            {{ optional($invite->expires_at)->format('d M Y') ?? '-' }}
                        </td>

                        <td class="px-6 py-4 text-right">
                            <a class="font-semibold text-teal-800 hover:text-teal-950"
                               href="{{ route('admin.onboarding.invites.show', $invite) }}">
                                View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-10 text-center text-slate-500">
                            No invites yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <div class="mt-6">
        {{ $invites->links() }}
    </div>

</x-layouts.admin>