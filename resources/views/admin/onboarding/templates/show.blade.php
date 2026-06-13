<x-layouts.admin
    title="Form Template"
    heading="Form Template"
    subheading="View template rules, required documents, and review checklist items.">

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <section class="xl:col-span-2 space-y-6">
            <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-900">{{ $template->name }}</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            {{ $template->description ?? 'No description provided.' }}
                        </p>
                    </div>

                    @if ($template->is_active)
                        <span class="rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-700">
                            Active
                        </span>
                    @else
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                            Inactive
                        </span>
                    @endif
                </div>

                <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="rounded-xl bg-slate-50 border border-slate-200 p-4">
                        <div class="text-sm text-slate-500">Default Expiry</div>
                        <div class="mt-2 text-2xl font-semibold">{{ $template->default_expiry_days }}</div>
                        <div class="text-xs text-slate-400">days</div>
                    </div>

                    <div class="rounded-xl bg-slate-50 border border-slate-200 p-4">
                        <div class="text-sm text-slate-500">Required Fields</div>
                        <div class="mt-2 text-2xl font-semibold">{{ count($template->required_fields ?? []) }}</div>
                    </div>

                    <div class="rounded-xl bg-slate-50 border border-slate-200 p-4">
                        <div class="text-sm text-slate-500">Documents</div>
                        <div class="mt-2 text-2xl font-semibold">{{ count($template->required_documents ?? []) }}</div>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
                <h2 class="text-lg font-semibold text-slate-900">Required Fields</h2>

                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                    @forelse ($template->required_fields ?? [] as $field)
                        <div class="rounded-xl border border-slate-200 px-4 py-3 text-sm">
                            {{ $field }}
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No required fields listed.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
                <h2 class="text-lg font-semibold text-slate-900">Required Documents</h2>

                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                    @forelse ($template->required_documents ?? [] as $document)
                        <div class="rounded-xl border border-orange-100 bg-orange-50 px-4 py-3 text-sm text-orange-800">
                            {{ $document }}
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No required documents listed.</p>
                    @endforelse
                </div>
            </div>
        </section>

        <aside class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6 h-fit">
            <h2 class="text-lg font-semibold text-slate-900">Review Checklist</h2>

            <div class="mt-4 space-y-3">
                @forelse ($template->review_checklist ?? [] as $item)
                    <div class="flex gap-3 rounded-xl border border-slate-200 p-3">
                        <div class="mt-1 h-4 w-4 rounded border border-slate-300"></div>
                        <div class="text-sm text-slate-700">{{ $item }}</div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No checklist items listed.</p>
                @endforelse
            </div>

            <div class="mt-6">
                <a href="{{ route('admin.onboarding.templates.index') }}"
                   class="inline-flex w-full justify-center rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Back to Templates
                </a>
            </div>
        </aside>
    </div>

</x-layouts.admin>