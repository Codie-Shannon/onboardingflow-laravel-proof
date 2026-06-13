<x-layouts.admin
    title="Create Form Template"
    heading="Create Form Template"
    subheading="Define a reusable onboarding template with fields, documents, and review checklist items.">

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <section class="xl:col-span-2 rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <strong>Please fix the following:</strong>
                    <ul class="mt-2 list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.onboarding.templates.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-slate-700">Template Name</label>
                    <input type="text"
                           name="name"
                           value="{{ old('name') }}"
                           required
                           placeholder="Contractor Onboarding"
                           class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700">Description</label>
                    <textarea name="description"
                              rows="3"
                              class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100"
                              placeholder="Used when onboarding contractors who need safety details and document checks.">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700">Default Expiry Days</label>
                    <input type="number"
                           name="default_expiry_days"
                           value="{{ old('default_expiry_days', 7) }}"
                           min="1"
                           max="90"
                           required
                           class="mt-2 w-40 rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700">Required Fields</label>
                    <p class="mt-1 text-xs text-slate-500">One item per line.</p>
                    <textarea name="required_fields_text"
                              rows="5"
                              class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">{{ old('required_fields_text', "First Name\nLast Name\nEmail\nPhone\nOrganisation\nRole\nEmergency Contact") }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700">Required Documents</label>
                    <p class="mt-1 text-xs text-slate-500">One item per line.</p>
                    <textarea name="required_documents_text"
                              rows="5"
                              class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">{{ old('required_documents_text', "Photo ID\nSigned Agreement\nRelevant Certificate\nInsurance Document") }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700">Review Checklist</label>
                    <p class="mt-1 text-xs text-slate-500">One item per line.</p>
                    <textarea name="review_checklist_text"
                              rows="5"
                              class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">{{ old('review_checklist_text', "Contact details checked\nOrganisation and role checked\nEmergency contact checked\nRequired documents checked\nReady for approval") }}</textarea>
                </div>

                <label class="flex items-center gap-3 text-sm font-medium text-slate-700">
                    <input type="checkbox"
                           name="is_active"
                           value="1"
                           checked
                           class="rounded border-slate-300 text-teal-800">
                    Active template
                </label>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit"
                            class="rounded-xl bg-teal-800 px-5 py-3 text-sm font-semibold text-white hover:bg-teal-900">
                        Create Template
                    </button>

                    <a href="{{ route('admin.onboarding.templates.index') }}"
                       class="rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Cancel
                    </a>
                </div>
            </form>
        </section>

        <aside class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6 h-fit">
            <h2 class="text-lg font-semibold text-slate-900">Why templates?</h2>
            <p class="mt-3 text-sm leading-6 text-slate-600">
                Templates turn the onboarding flow from one fixed form into a reusable module.
                Different onboarding types can have different fields, document expectations,
                and review steps.
            </p>
        </aside>
    </div>

</x-layouts.admin>