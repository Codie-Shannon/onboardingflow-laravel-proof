<x-layouts.admin
    title="Create Onboarding Invite"
    heading="Create Invite"
    subheading="Create a secure onboarding link for a worker, contractor, client, or applicant.">

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

            <form method="POST" action="{{ route('admin.onboarding.invites.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-slate-700">Onboarding Template</label>
                    <select name="onboarding_template_id"
                            class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
                        <option value="">No template selected</option>

                        @foreach ($templates as $template)
                            <option value="{{ $template->id }}" @selected(old('onboarding_template_id') == $template->id)>
                                {{ $template->name }}
                            </option>
                        @endforeach
                    </select>

                    <p class="mt-1 text-xs text-slate-500">
                        Choose the onboarding template that matches this invite.
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700">Recipient Name</label>
                    <input type="text"
                           name="recipient_name"
                           value="{{ old('recipient_name') }}"
                           required
                           class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700">Recipient Email</label>
                    <input type="email"
                           name="recipient_email"
                           value="{{ old('recipient_email') }}"
                           required
                           class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Role / Position</label>
                        <input type="text"
                               name="role"
                               value="{{ old('role') }}"
                               class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Organisation</label>
                        <input type="text"
                               name="organisation"
                               value="{{ old('organisation') }}"
                               class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700">Message</label>
                    <textarea name="message"
                              rows="5"
                              class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100"
                              placeholder="Optional message shown on the public onboarding form.">{{ old('message') }}</textarea>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit"
                            class="rounded-xl bg-teal-800 px-5 py-3 text-sm font-semibold text-white hover:bg-teal-900">
                        Create Invite
                    </button>

                    <a href="{{ route('admin.onboarding.invites.index') }}"
                       class="rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Cancel
                    </a>
                </div>
            </form>
        </section>

        <aside class="space-y-6">
            <section class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
                <h2 class="text-lg font-semibold text-slate-900">Workflow</h2>

                <div class="mt-5 space-y-4">
                    <div class="flex gap-3">
                        <div class="mt-1 w-2 h-2 rounded-full bg-teal-700"></div>
                        <div>
                            <div class="text-sm font-semibold">Choose template</div>
                            <div class="text-sm text-slate-500">Select the onboarding type for this invite.</div>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <div class="mt-1 w-2 h-2 rounded-full bg-teal-700"></div>
                        <div>
                            <div class="text-sm font-semibold">Create invite</div>
                            <div class="text-sm text-slate-500">Admin enters basic recipient details.</div>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <div class="mt-1 w-2 h-2 rounded-full bg-teal-700"></div>
                        <div>
                            <div class="text-sm font-semibold">Generate link</div>
                            <div class="text-sm text-slate-500">System creates a unique public onboarding URL.</div>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <div class="mt-1 w-2 h-2 rounded-full bg-teal-700"></div>
                        <div>
                            <div class="text-sm font-semibold">Review</div>
                            <div class="text-sm text-slate-500">Missing details and review status are tracked.</div>
                        </div>
                    </div>
                </div>
            </section>
        </aside>
    </div>

</x-layouts.admin>