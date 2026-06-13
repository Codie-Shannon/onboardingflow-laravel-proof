<x-layouts.public-onboarding
    title="Complete Onboarding Form"
    heading="Complete Onboarding Form"
    subheading="Please complete the onboarding details below. Your submission will be reviewed by the team.">

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

            <form method="POST" action="{{ route('public.onboarding.store', $invite->token) }}" class="space-y-8">
                @csrf

                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Personal Details</h2>
                    <p class="mt-1 text-sm text-slate-500">Tell us who you are and how to contact you.</p>

                    <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700">First Name</label>
                            <input type="text"
                                   name="first_name"
                                   value="{{ old('first_name') }}"
                                   required
                                   class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700">Last Name</label>
                            <input type="text"
                                   name="last_name"
                                   value="{{ old('last_name') }}"
                                   required
                                   class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700">Email</label>
                            <input type="email"
                                   name="email"
                                   value="{{ old('email', $invite->recipient_email) }}"
                                   required
                                   class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700">Phone</label>
                            <input type="text"
                                   name="phone"
                                   value="{{ old('phone') }}"
                                   class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-200 pt-8">
                    <h2 class="text-lg font-semibold text-slate-900">Work Details</h2>
                    <p class="mt-1 text-sm text-slate-500">Confirm the organisation and role this onboarding relates to.</p>

                    <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700">Organisation</label>
                            <input type="text"
                                   name="organisation"
                                   value="{{ old('organisation', $invite->organisation) }}"
                                   class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700">Role / Position</label>
                            <input type="text"
                                   name="role"
                                   value="{{ old('role', $invite->role) }}"
                                   class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-200 pt-8">
                    <h2 class="text-lg font-semibold text-slate-900">Emergency Contact</h2>
                    <p class="mt-1 text-sm text-slate-500">These details help the team follow up if needed.</p>

                    <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700">Emergency Contact Name</label>
                            <input type="text"
                                   name="emergency_contact_name"
                                   value="{{ old('emergency_contact_name') }}"
                                   class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700">Emergency Contact Phone</label>
                            <input type="text"
                                   name="emergency_contact_phone"
                                   value="{{ old('emergency_contact_phone') }}"
                                   class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-200 pt-8">
                    <h2 class="text-lg font-semibold text-slate-900">Additional Notes</h2>

                    <textarea name="notes"
                              rows="5"
                              class="mt-5 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100"
                              placeholder="Add anything the team should know.">{{ old('notes') }}</textarea>
                </div>

                <div class="border-t border-slate-200 pt-6 flex items-center justify-end">
                    <button type="submit"
                            class="rounded-xl bg-teal-800 px-6 py-3 text-sm font-semibold text-white hover:bg-teal-900">
                        Submit Onboarding Form
                    </button>
                </div>
            </form>
        </section>

        <aside class="space-y-6">
            <section class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
                <h2 class="text-lg font-semibold text-slate-900">Invite Details</h2>

                <div class="mt-5 space-y-4 text-sm">
                    <div>
                        <div class="text-slate-500">Recipient</div>
                        <div class="font-medium text-slate-900">{{ $invite->recipient_name }}</div>
                    </div>

                    @if ($invite->template)
                        <div>
                            <div class="text-slate-500">Template</div>
                            <div class="font-medium text-slate-900">{{ $invite->template->name }}</div>
                        </div>
                    @endif

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
                </div>
            </section>

            @if ($invite->message)
                <section class="rounded-2xl bg-teal-50 border border-teal-100 shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-teal-950">Message</h2>
                    <p class="mt-3 text-sm leading-6 text-teal-900">{{ $invite->message }}</p>
                </section>
            @endif

            <section class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
                <h2 class="text-lg font-semibold text-slate-900">What happens next?</h2>

                <div class="mt-5 space-y-4">
                    <div class="flex gap-3">
                        <div class="mt-1 h-2 w-2 rounded-full bg-teal-700"></div>
                        <div>
                            <div class="text-sm font-semibold">Submit details</div>
                            <div class="text-sm text-slate-500">Your information is saved securely in the proof workflow.</div>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <div class="mt-1 h-2 w-2 rounded-full bg-teal-700"></div>
                        <div>
                            <div class="text-sm font-semibold">Admin review</div>
                            <div class="text-sm text-slate-500">The team reviews your submission and checks for missing information.</div>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <div class="mt-1 h-2 w-2 rounded-full bg-teal-700"></div>
                        <div>
                            <div class="text-sm font-semibold">Follow-up if needed</div>
                            <div class="text-sm text-slate-500">If anything is missing, it can be tracked instead of lost in email.</div>
                        </div>
                    </div>
                </div>
            </section>
        </aside>
    </div>

</x-layouts.public-onboarding>