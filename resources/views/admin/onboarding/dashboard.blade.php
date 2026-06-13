<x-layouts.admin
    title="Onboarding Overview"
    heading="Onboarding Overview"
    subheading="Track onboarding invites, submissions, missing information, and review activity.">

    <div class="grid grid-cols-1 md:grid-cols-5 gap-5 mb-8">
        <div class="rounded-2xl bg-white border border-slate-200 p-5 shadow-sm">
            <div class="text-sm text-slate-500">Total Invites</div>
            <div class="mt-3 text-3xl font-semibold">{{ $totalInvites }}</div>
        </div>

        <div class="rounded-2xl bg-white border border-slate-200 p-5 shadow-sm">
            <div class="text-sm text-slate-500">Submitted</div>
            <div class="mt-3 text-3xl font-semibold text-blue-700">{{ $submitted }}</div>
        </div>

        <div class="rounded-2xl bg-white border border-slate-200 p-5 shadow-sm">
            <div class="text-sm text-slate-500">In Review</div>
            <div class="mt-3 text-3xl font-semibold text-amber-600">{{ $inReview }}</div>
        </div>

        <div class="rounded-2xl bg-white border border-slate-200 p-5 shadow-sm">
            <div class="text-sm text-slate-500">Needs Info</div>
            <div class="mt-3 text-3xl font-semibold text-orange-600">{{ $needsInfo }}</div>
        </div>

        <div class="rounded-2xl bg-white border border-slate-200 p-5 shadow-sm">
            <div class="text-sm text-slate-500">Approved</div>
            <div class="mt-3 text-3xl font-semibold text-green-700">{{ $approved }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <section class="xl:col-span-2 rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200 flex items-center justify-between">
                <h2 class="font-semibold">Recent Onboarding Invites</h2>
                <a class="text-sm font-medium text-teal-800" href="{{ route('admin.onboarding.invites.index') }}">View all</a>
            </div>

            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="text-left px-6 py-3">Name</th>
                        <th class="text-left px-6 py-3">Role</th>
                        <th class="text-left px-6 py-3">Status</th>
                        <th class="text-left px-6 py-3">Missing</th>
                        <th class="text-right px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($recentInvites as $invite)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="font-medium">{{ $invite->recipient_name }}</div>
                                <div class="text-slate-500">{{ $invite->recipient_email }}</div>
                            </td>
                            <td class="px-6 py-4">{{ $invite->role ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <span class="rounded-full bg-teal-50 px-3 py-1 text-xs font-semibold text-teal-800">
                                    {{ $invite->statusLabel() }}
                                </span>
                            </td>
                            <td class="px-6 py-4">{{ $invite->unresolved_missing_info_items_count ?? 0 }}</td>
                            <td class="px-6 py-4 text-right">
                                <a class="text-teal-800 font-medium" href="{{ route('admin.onboarding.invites.show', $invite) }}">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-500">No invites yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>

        <section class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
            <h2 class="font-semibold">Onboarding Progress</h2>
            <div class="mt-6 flex items-center justify-center">
                <div class="w-40 h-40 rounded-full border-[18px] border-teal-600 flex items-center justify-center">
                    <div class="text-center">
                        <div class="text-3xl font-semibold">{{ $completionPercent }}%</div>
                        <div class="text-xs text-slate-500">Approved</div>
                    </div>
                </div>
            </div>
        </section>

        <section class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-semibold">Review Queue</h2>
            </div>

            <div class="space-y-3">
                @forelse ($reviewQueue as $invite)
                    <a href="{{ route('admin.onboarding.invites.show', $invite) }}"
                       class="block rounded-xl border border-slate-200 p-4 hover:bg-slate-50">
                        <div class="font-medium">{{ $invite->recipient_name }}</div>
                        <div class="text-sm text-slate-500">{{ $invite->statusLabel() }}</div>
                    </a>
                @empty
                    <p class="text-sm text-slate-500">Nothing waiting for review.</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
            <h2 class="font-semibold mb-4">Missing Information Alerts</h2>

            <div class="space-y-3">
                @forelse ($missingInfoItems as $item)
                    <a href="{{ $item->invite ? route('admin.onboarding.invites.show', $item->invite) : '#' }}"
                       class="block rounded-xl border border-orange-100 bg-orange-50 p-4">
                        <div class="font-medium text-orange-800">{{ $item->label }}</div>
                        <div class="text-sm text-orange-700">{{ $item->invite?->recipient_name ?? '-' }}</div>
                    </a>
                @empty
                    <p class="text-sm text-slate-500">No missing information alerts.</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
            <h2 class="font-semibold mb-4">Recent Activity</h2>

            <div class="space-y-3">
                @forelse ($recentActivity as $activity)
                    <div class="rounded-xl border border-slate-200 p-4">
                        <div class="text-sm font-medium">{{ ucwords(str_replace('_', ' ', $activity->action)) }}</div>
                        <div class="text-sm text-slate-500">{{ $activity->description ?? '-' }}</div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No activity yet.</p>
                @endforelse
            </div>
        </section>
    </div>

</x-layouts.admin>