<x-layouts.public
    title="Complete Onboarding"
    heading="{{ ($isResubmission ?? false) ? 'Update Your Onboarding' : 'Complete Your Onboarding' }}"
    subheading="{{ ($isResubmission ?? false) ? 'Please update the missing information requested by the reviewer.' : 'Please complete the onboarding form below.' }}"
>
    <div class="mx-auto max-w-4xl space-y-6">
        @if ($isResubmission ?? false)
            <section class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-amber-900">
                <h2 class="text-lg font-semibold">
                    More information needed
                </h2>

                <p class="mt-2 text-sm">
                    Please update the missing details below and resubmit your onboarding information.
                </p>
            </section>
        @endif

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">
                        Invite Details
                    </h2>

                    <p class="mt-1 text-sm text-slate-600">
                        This onboarding request was created for {{ $invite->recipient_name }}.
                    </p>
                </div>

                <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                    {{ $invite->template?->name ?? 'Onboarding' }}
                </span>
            </div>

            <dl class="mt-5 grid gap-4 sm:grid-cols-2">
                <div class="rounded-xl bg-slate-50 p-4">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                        Name
                    </dt>
                    <dd class="mt-1 text-sm font-medium text-slate-900">
                        {{ $invite->recipient_name }}
                    </dd>
                </div>

                <div class="rounded-xl bg-slate-50 p-4">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                        Email
                    </dt>
                    <dd class="mt-1 text-sm font-medium text-slate-900">
                        {{ $invite->recipient_email }}
                    </dd>
                </div>

                @if ($invite->organisation)
                    <div class="rounded-xl bg-slate-50 p-4">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Organisation
                        </dt>
                        <dd class="mt-1 text-sm font-medium text-slate-900">
                            {{ $invite->organisation }}
                        </dd>
                    </div>
                @endif

                @if ($invite->role)
                    <div class="rounded-xl bg-slate-50 p-4">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Role / Position
                        </dt>
                        <dd class="mt-1 text-sm font-medium text-slate-900">
                            {{ $invite->role }}
                        </dd>
                    </div>
                @endif
            </dl>

            @if ($invite->message)
                <div class="mt-5 rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                        Message
                    </div>

                    <p class="mt-2 whitespace-pre-wrap text-sm text-slate-700">
                        {{ $invite->message }}
                    </p>
                </div>
            @endif
        </section>

        @if (($isResubmission ?? false) && $invite->missingInfoItems->where('resolved', false)->isNotEmpty())
            <section class="rounded-2xl border border-red-200 bg-red-50 p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-red-900">
                    Missing Information
                </h2>

                <p class="mt-1 text-sm text-red-800">
                    The reviewer has asked for the following information to be updated.
                </p>

                <ul class="mt-4 space-y-3">
                    @foreach ($invite->missingInfoItems->where('resolved', false) as $item)
                        <li class="rounded-xl bg-white/80 p-4">
                            <div class="text-sm font-semibold text-red-900">
                                {{ $item->label }}
                            </div>

                            @if ($item->field_name && $item->field_name !== $item->label)
                                <div class="mt-1 text-xs text-red-700">
                                    Field: {{ $item->field_name }}
                                </div>
                            @endif

                            @foreach ($item->followUps->where('status', 'open') as $followUp)
                                <div class="mt-3 rounded-xl border border-red-100 bg-red-50 p-3">
                                    <div class="text-xs font-semibold uppercase tracking-wide text-red-700">
                                        Reviewer follow-up
                                    </div>

                                    <p class="mt-1 whitespace-pre-wrap text-sm text-red-800">
                                        {{ $followUp->message }}
                                    </p>

                                    @if ($followUp->due_at)
                                        <div class="mt-2 text-xs font-medium text-red-700">
                                            Due: {{ $followUp->due_at->format('d M Y') }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        @if ($errors->any())
            <section class="rounded-2xl border border-red-200 bg-red-50 p-5 text-red-800">
                <h2 class="text-sm font-semibold">
                    Please check the form and try again.
                </h2>

                <ul class="mt-2 list-disc space-y-1 pl-5 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </section>
        @endif

        <form
            method="POST"
            action="{{ route('public.onboarding.store', $invite->token) }}"
            enctype="multipart/form-data"
            class="space-y-6"
        >
            @csrf

            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">
                    Your Details
                </h2>

                <p class="mt-1 text-sm text-slate-600">
                    Please provide your contact and onboarding details.
                </p>

                <div class="mt-6 grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="first_name" class="text-sm font-medium text-slate-700">
                            First Name
                        </label>

                        <input
                            id="first_name"
                            name="first_name"
                            type="text"
                            value="{{ old('first_name', $invite->submission?->first_name) }}"
                            required
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-200"
                        >

                        @error('first_name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="last_name" class="text-sm font-medium text-slate-700">
                            Last Name
                        </label>

                        <input
                            id="last_name"
                            name="last_name"
                            type="text"
                            value="{{ old('last_name', $invite->submission?->last_name) }}"
                            required
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-200"
                        >

                        @error('last_name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="text-sm font-medium text-slate-700">
                            Email
                        </label>

                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email', $invite->submission?->email ?? $invite->recipient_email) }}"
                            required
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-200"
                        >

                        @error('email')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="text-sm font-medium text-slate-700">
                            Phone
                        </label>

                        <input
                            id="phone"
                            name="phone"
                            type="text"
                            value="{{ old('phone', $invite->submission?->phone) }}"
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-200"
                        >

                        @error('phone')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="organisation" class="text-sm font-medium text-slate-700">
                            Organisation
                        </label>

                        <input
                            id="organisation"
                            name="organisation"
                            type="text"
                            value="{{ old('organisation', $invite->submission?->organisation ?? $invite->organisation) }}"
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-200"
                        >

                        @error('organisation')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="role" class="text-sm font-medium text-slate-700">
                            Role / Position
                        </label>

                        <input
                            id="role"
                            name="role"
                            type="text"
                            value="{{ old('role', $invite->submission?->role ?? $invite->role) }}"
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-200"
                        >

                        @error('role')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="emergency_contact_name" class="text-sm font-medium text-slate-700">
                            Emergency Contact Name
                        </label>

                        <input
                            id="emergency_contact_name"
                            name="emergency_contact_name"
                            type="text"
                            value="{{ old('emergency_contact_name', $invite->submission?->emergency_contact_name) }}"
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-200"
                        >

                        @error('emergency_contact_name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="emergency_contact_phone" class="text-sm font-medium text-slate-700">
                            Emergency Contact Phone
                        </label>

                        <input
                            id="emergency_contact_phone"
                            name="emergency_contact_phone"
                            type="text"
                            value="{{ old('emergency_contact_phone', $invite->submission?->emergency_contact_phone) }}"
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-200"
                        >

                        @error('emergency_contact_phone')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-5">
                    <label for="notes" class="text-sm font-medium text-slate-700">
                        Notes
                    </label>

                    <textarea
                        id="notes"
                        name="notes"
                        rows="4"
                        class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-200"
                    >{{ old('notes', $invite->submission?->notes) }}</textarea>

                    @error('notes')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </section>

            @if ($invite->documentRequirements->isNotEmpty())
                <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-slate-900">
                        Required Documents
                    </h2>

                    <p class="mt-1 text-sm text-slate-600">
                        Upload the documents requested for this onboarding. Files are stored in SharePoint.
                    </p>

                    <div class="mt-6 space-y-4">
                        @foreach ($invite->documentRequirements->sortBy('sort_order') as $requirement)
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                    <div>
                                        <h3 class="text-sm font-semibold text-slate-900">
                                            {{ $requirement->label }}
                                        </h3>

                                        @if ($requirement->description)
                                            <p class="mt-1 text-sm text-slate-600">
                                                {{ $requirement->description }}
                                            </p>
                                        @endif

                                        <div class="mt-2">
                                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold
                                                @class([
                                                    'bg-red-100 text-red-700' => $requirement->status === 'missing',
                                                    'bg-blue-100 text-blue-700' => $requirement->status === 'provided',
                                                    'bg-emerald-100 text-emerald-700' => $requirement->status === 'reviewed',
                                                    'bg-slate-200 text-slate-600' => $requirement->status === 'not_required',
                                                ])
                                            ">
                                                {{ $requirement->statusLabel() }}
                                            </span>
                                        </div>
                                    </div>

                                    @if ($requirement->hasUploadedFile())
                                        <div class="rounded-xl bg-white px-4 py-3 text-sm text-slate-700 sm:min-w-64">
                                            <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                                Current File
                                            </div>

                                            <div class="mt-1 font-medium text-slate-900">
                                                {{ $requirement->uploaded_original_name }}
                                            </div>

                                            <div class="mt-1 text-xs text-slate-500">
                                                {{ $requirement->uploaded_at?->format('d M Y, g:i A') }}
                                                @if ($requirement->uploadedFileSizeLabel())
                                                    · {{ $requirement->uploadedFileSizeLabel() }}
                                                @endif
                                            </div>

                                            @if ($requirement->sharepoint_web_url)
                                                <a
                                                    href="{{ $requirement->sharepoint_web_url }}"
                                                    target="_blank"
                                                    class="mt-2 inline-flex text-xs font-semibold text-teal-700 hover:text-teal-800"
                                                >
                                                    Open current file
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                @if ($requirement->upload_error)
                                    <div class="mt-4 rounded-xl border border-red-200 bg-red-50 p-3 text-xs text-red-700">
                                        {{ $requirement->upload_error }}
                                    </div>
                                @endif

                                <div class="mt-4">
                                    <label
                                        for="document_{{ $requirement->id }}"
                                        class="text-sm font-medium text-slate-700"
                                    >
                                        {{ $requirement->hasUploadedFile() ? 'Replace file' : 'Upload file' }}
                                    </label>

                                    <input
                                        id="document_{{ $requirement->id }}"
                                        name="documents[{{ $requirement->id }}]"
                                        type="file"
                                        class="mt-1 block w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 shadow-sm file:mr-4 file:rounded-lg file:border-0 file:bg-teal-50 file:px-3 file:py-2 file:text-sm file:font-medium file:text-teal-700 hover:file:bg-teal-100"
                                    >

                                    @error("documents.{$requirement->id}")
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">
                            {{ ($isResubmission ?? false) ? 'Resubmit Onboarding' : 'Submit Onboarding' }}
                        </h2>

                        <p class="mt-1 text-sm text-slate-600">
                            {{ ($isResubmission ?? false)
                                ? 'Your updated information will be sent back for review.'
                                : 'Your information will be sent to the review team.'
                            }}
                        </p>
                    </div>

                    <button
                        type="submit"
                        class="inline-flex rounded-xl bg-teal-700 px-5 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-teal-800"
                    >
                        {{ ($isResubmission ?? false) ? 'Resubmit Onboarding' : 'Submit Onboarding' }}
                    </button>
                </div>
            </section>
        </form>
    </div>
</x-layouts.public>