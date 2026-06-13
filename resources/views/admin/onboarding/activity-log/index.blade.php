<x-layouts.admin
    title="Activity Log"
    heading="Activity Log"
    subheading="Review onboarding workflow activity across all invites.">

    <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500">
                <tr>
                    <th class="text-left px-6 py-3">Created</th>
                    <th class="text-left px-6 py-3">Actor</th>
                    <th class="text-left px-6 py-3">Action</th>
                    <th class="text-left px-6 py-3">Description</th>
                    <th class="text-right px-6 py-3">Invite</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100">
                @forelse ($activityLogs as $activity)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 text-slate-600">
                            {{ $activity->created_at->format('d M Y H:i') }}
                        </td>

                        <td class="px-6 py-4 font-medium text-slate-900">
                            {{ $activity->actor_name }}
                        </td>

                        <td class="px-6 py-4">
                            <span class="rounded-full bg-teal-50 px-3 py-1 text-xs font-semibold text-teal-800">
                                {{ ucwords(str_replace('_', ' ', $activity->action)) }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-slate-600">
                            {{ $activity->description ?? '-' }}
                        </td>

                        <td class="px-6 py-4 text-right">
                            @if ($activity->invite)
                                <a class="font-semibold text-teal-800 hover:text-teal-950"
                                   href="{{ route('admin.onboarding.invites.show', $activity->invite) }}">
                                    {{ $activity->invite->recipient_name }}
                                </a>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-slate-500">
                            No activity yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <div class="mt-6">
        {{ $activityLogs->links() }}
    </div>

</x-layouts.admin>