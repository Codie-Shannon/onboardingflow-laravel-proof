<x-layouts.admin
    title="Form Templates"
    heading="Form Templates"
    subheading="Create reusable onboarding templates for different onboarding scenarios.">

    @php
        $currentUser = auth()->user();
    @endphp

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-slate-900">Template Library</h2>
            <p class="text-sm text-slate-500">
                Templates define required fields, document requirements, and review checklist items.
            </p>
        </div>

        @if ($currentUser?->isAdmin())
            <a href="{{ route('admin.onboarding.templates.create') }}"
               class="rounded-xl bg-teal-800 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-900">
                + Create Template
            </a>
        @endif
    </div>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500">
                <tr>
                    <th class="text-left px-6 py-3">Template</th>
                    <th class="text-left px-6 py-3">Expiry</th>
                    <th class="text-left px-6 py-3">Fields</th>
                    <th class="text-left px-6 py-3">Documents</th>
                    <th class="text-left px-6 py-3">Checklist</th>
                    <th class="text-left px-6 py-3">Status</th>
                    <th class="text-right px-6 py-3"></th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100">
                @forelse ($templates as $template)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4">
                            <div class="font-medium text-slate-900">{{ $template->name }}</div>
                            <div class="text-xs text-slate-500">{{ $template->description ?? 'No description' }}</div>
                        </td>

                        <td class="px-6 py-4 text-slate-600">
                            {{ $template->default_expiry_days }} days
                        </td>

                        <td class="px-6 py-4">
                            {{ count($template->required_fields ?? []) }}
                        </td>

                        <td class="px-6 py-4">
                            {{ count($template->required_documents ?? []) }}
                        </td>

                        <td class="px-6 py-4">
                            {{ count($template->review_checklist ?? []) }}
                        </td>

                        <td class="px-6 py-4">
                            @if ($template->is_active)
                                <span class="rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-700">
                                    Active
                                </span>
                            @else
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                    Inactive
                                </span>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.onboarding.templates.show', $template) }}"
                               class="font-semibold text-teal-800 hover:text-teal-950">
                                View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-slate-500">
                            No templates yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <div class="mt-6">
        {{ $templates->links() }}
    </div>

</x-layouts.admin>