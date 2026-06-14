<x-layouts.admin
    heading="Reports"
    subheading="Review onboarding workflow performance, missing information, checklist progress, and document tracking."
>
    @php
        $currentUser = auth()->user();
    @endphp
    <div class="space-y-6">
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="text-sm font-medium text-slate-500">Total Invites</div>
                <div class="mt-2 text-3xl font-bold text-slate-900">{{ $totalInvites }}</div>
                <p class="mt-2 text-sm text-slate-600">All onboarding invites created.</p>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="text-sm font-medium text-slate-500">Submitted</div>
                <div class="mt-2 text-3xl font-bold text-slate-900">{{ $submittedInvites }}</div>
                <p class="mt-2 text-sm text-slate-600">{{ $submissionRate }}% submission rate.</p>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="text-sm font-medium text-slate-500">Needs Info</div>
                <div class="mt-2 text-3xl font-bold text-amber-700">{{ $needsInfoInvites }}</div>
                <p class="mt-2 text-sm text-slate-600">Invites currently waiting on missing information.</p>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="text-sm font-medium text-slate-500">Approved</div>
                <div class="mt-2 text-3xl font-bold text-emerald-700">{{ $approvedInvites }}</div>
                <p class="mt-2 text-sm text-slate-600">{{ $approvalRate }}% approval rate.</p>
            </section>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Status Breakdown</h2>
                        <p class="mt-1 text-sm text-slate-600">
                            Current invite status distribution.
                        </p>
                    </div>
                </div>

                <div class="mt-5 space-y-3">
                    @foreach ($statusCounts as $status => $count)
                        @php
                            $percent = $totalInvites > 0 ? round(($count / $totalInvites) * 100) : 0;
                            $label = ucwords(str_replace('_', ' ', $status));
                        @endphp

                        <div>
                            <div class="mb-1 flex items-center justify-between text-sm">
                                <span class="font-medium text-slate-700">{{ $label }}</span>
                                <span class="text-slate-500">{{ $count }} / {{ $totalInvites }} · {{ $percent }}%</span>
                            </div>

                            <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                                <div
                                    class="h-2 rounded-full bg-teal-600"
                                    style="width: {{ $percent }}%"
                                ></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Template Usage</h2>
                        <p class="mt-1 text-sm text-slate-600">
                            How often each onboarding template has been used.
                        </p>
                    </div>

                    <a
                        href="{{ route('admin.onboarding.templates.index') }}"
                        class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50"
                    >
                        View Templates
                    </a>
                </div>

                <div class="mt-5 overflow-hidden rounded-xl border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-slate-700">Template</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-700">Invites</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @forelse ($templates as $template)
                                <tr>
                                    <td class="px-4 py-3">
                                        <a
                                            href="{{ route('admin.onboarding.templates.show', $template) }}"
                                            class="font-medium text-teal-700 hover:text-teal-800 hover:underline"
                                        >
                                            {{ $template->name }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 text-slate-700">{{ $template->invites_count }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-4 py-6 text-center text-slate-500">
                                        No templates created yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <div class="grid gap-6 xl:grid-cols-3">
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Missing Info</h2>
                <p class="mt-1 text-sm text-slate-600">Resolved versus open missing information items.</p>

                <div class="mt-5 grid gap-3">
                    <div class="rounded-xl bg-slate-50 p-4">
                        <div class="text-sm font-medium text-slate-500">Open</div>
                        <div class="mt-1 text-2xl font-bold text-red-700">{{ $openMissingInfoItems }}</div>
                    </div>

                    <div class="rounded-xl bg-slate-50 p-4">
                        <div class="text-sm font-medium text-slate-500">Resolved</div>
                        <div class="mt-1 text-2xl font-bold text-emerald-700">{{ $resolvedMissingInfoItems }}</div>
                    </div>

                    <div class="rounded-xl bg-slate-50 p-4">
                        <div class="text-sm font-medium text-slate-500">Resolution Rate</div>
                        <div class="mt-1 text-2xl font-bold text-slate-900">{{ $missingInfoResolutionRate }}%</div>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Review Checklist</h2>
                <p class="mt-1 text-sm text-slate-600">Completion progress across all invite checklist items.</p>

                <div class="mt-5 rounded-xl bg-slate-50 p-4">
                    <div class="text-sm font-medium text-slate-500">Completed</div>
                    <div class="mt-1 text-2xl font-bold text-slate-900">
                        {{ $completedChecklistItems }} / {{ $totalChecklistItems }}
                    </div>
                    <div class="mt-3 h-2 overflow-hidden rounded-full bg-slate-200">
                        <div
                            class="h-2 rounded-full bg-emerald-600"
                            style="width: {{ $checklistCompletionRate }}%"
                        ></div>
                    </div>
                    <p class="mt-2 text-sm text-slate-600">{{ $checklistCompletionRate }}% complete.</p>
                </div>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Follow-ups</h2>
                <p class="mt-1 text-sm text-slate-600">Needs-info follow-up status counts.</p>

                <div class="mt-5 space-y-3">
                    <div class="flex items-center justify-between rounded-xl bg-slate-50 p-4">
                        <span class="text-sm font-medium text-slate-600">Open</span>
                        <span class="text-xl font-bold text-amber-700">{{ $followUpSummary['open'] }}</span>
                    </div>

                    <div class="flex items-center justify-between rounded-xl bg-slate-50 p-4">
                        <span class="text-sm font-medium text-slate-600">Resolved</span>
                        <span class="text-xl font-bold text-emerald-700">{{ $followUpSummary['resolved'] }}</span>
                    </div>

                    <div class="flex items-center justify-between rounded-xl bg-slate-50 p-4">
                        <span class="text-sm font-medium text-slate-600">Cancelled</span>
                        <span class="text-xl font-bold text-slate-700">{{ $followUpSummary['cancelled'] }}</span>
                    </div>
                </div>
            </section>
        </div>

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Document Requirements</h2>
                    <p class="mt-1 text-sm text-slate-600">
                        Status summary for required onboarding documents.
                    </p>
                </div>

                <div class="rounded-full bg-slate-100 px-3 py-1 text-sm font-medium text-slate-700">
                    {{ $documentReviewRate }}% reviewed
                </div>
            </div>

            <div class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-xl bg-red-50 p-4">
                    <div class="text-sm font-medium text-red-700">Missing</div>
                    <div class="mt-1 text-2xl font-bold text-red-800">{{ $documentRequirementCounts['missing'] }}</div>
                </div>

                <div class="rounded-xl bg-blue-50 p-4">
                    <div class="text-sm font-medium text-blue-700">Provided</div>
                    <div class="mt-1 text-2xl font-bold text-blue-800">{{ $documentRequirementCounts['provided'] }}</div>
                </div>

                <div class="rounded-xl bg-emerald-50 p-4">
                    <div class="text-sm font-medium text-emerald-700">Reviewed</div>
                    <div class="mt-1 text-2xl font-bold text-emerald-800">{{ $documentRequirementCounts['reviewed'] }}</div>
                </div>

                <div class="rounded-xl bg-slate-50 p-4">
                    <div class="text-sm font-medium text-slate-700">Not Required</div>
                    <div class="mt-1 text-2xl font-bold text-slate-800">{{ $documentRequirementCounts['not_required'] }}</div>
                </div>
            </div>
        </section>

        <div class="grid gap-6 xl:grid-cols-2">
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Needs Info Queue</h2>
                        <p class="mt-1 text-sm text-slate-600">
                            Recent invites still waiting on missing information.
                        </p>
                    </div>

                    <a
                        href="{{ route('admin.onboarding.invites.index') }}"
                        class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50"
                    >
                        View Invites
                    </a>
                </div>

                <div class="mt-5 space-y-3">
                    @forelse ($recentNeedsInfoInvites as $invite)
                        <a
                            href="{{ route('admin.onboarding.invites.show', $invite) }}"
                            class="block rounded-xl border border-slate-200 p-4 hover:bg-slate-50"
                        >
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <div class="font-medium text-slate-900">{{ $invite->recipient_name }}</div>
                                    <div class="mt-1 text-sm text-slate-500">
                                        {{ $invite->template?->name ?? 'No template' }}
                                    </div>
                                </div>

                                <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-medium text-amber-700">
                                    {{ $invite->unresolved_missing_info_items_count }} open
                                </span>
                            </div>
                        </a>
                    @empty
                        <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-6 text-center text-sm text-slate-600">
                            No invites currently need information.
                        </div>
                    @endforelse
                </div>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Exports</h2>
                        <p class="mt-1 text-sm text-slate-600">
                            Download onboarding submission data for spreadsheet review.
                        </p>
                    </div>
                </div>

                @if ($currentUser?->canReview())
                    <div class="mt-5 rounded-xl border border-slate-200 bg-slate-50 p-5">
                        <div class="font-medium text-slate-900">Submission CSV Export</div>
                        <p class="mt-1 text-sm text-slate-600">
                            Includes invite, template, submission, status, and missing information summary fields.
                        </p>

                        <a
                            href="{{ route('admin.onboarding.exports.submissions') }}"
                            class="mt-4 inline-flex rounded-xl bg-teal-700 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-teal-800"
                        >
                            Download CSV
                        </a>
                    </div>
                @else
                    <div class="mt-5 rounded-xl border border-slate-200 bg-slate-50 p-5 text-sm text-slate-600">
                        CSV export is available to Admin and Reviewer users only.
                    </div>
                @endif

                <div class="mt-5">
                    <h3 class="text-sm font-semibold text-slate-900">Recent Activity</h3>

                    <div class="mt-3 space-y-3">
                        @forelse ($recentActivity as $activity)
                            <div class="rounded-xl border border-slate-200 p-3">
                                <div class="flex items-center justify-between gap-3">
                                    <span class="text-sm font-medium text-slate-900">
                                        {{ ucwords(str_replace('_', ' ', $activity->action)) }}
                                    </span>

                                    <span class="text-xs text-slate-500">
                                        {{ $activity->created_at->format('d M Y H:i') }}
                                    </span>
                                </div>

                                <p class="mt-1 text-sm text-slate-600">
                                    {{ $activity->description ?? '-' }}
                                </p>
                            </div>
                        @empty
                            <p class="text-sm text-slate-600">No recent activity yet.</p>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-layouts.admin>