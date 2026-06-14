<x-layouts.public-onboarding
    title="Onboarding Submitted"
    heading="Thank you"
    subheading="Your onboarding form has been submitted successfully.">

    <section class="mx-auto max-w-2xl rounded-2xl bg-white border border-slate-200 shadow-sm p-8 text-center">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-green-50 text-green-700 text-3xl">
            ✓
        </div>

        @if ($isResubmission ?? false)
            <h1 class="text-2xl font-bold text-slate-900">
                Updated information submitted
            </h1>

            <p class="mt-3 text-slate-600">
                Thanks — your updated onboarding information has been resubmitted for review.
            </p>
        @else
            <h1 class="text-2xl font-bold text-slate-900">
                Onboarding submitted
            </h1>

            <p class="mt-3 text-slate-600">
                Thanks — your onboarding information has been submitted for review.
            </p>
        @endif

        <div class="mt-6 rounded-xl bg-slate-50 border border-slate-200 p-4 text-left">
            <div class="text-sm font-semibold text-slate-900">What happens next?</div>
            <ul class="mt-3 space-y-2 text-sm text-slate-600">
                <li>• Your onboarding details are added to the review queue.</li>
                <li>• Missing information can be flagged for follow-up.</li>
                <li>• Staff can track review status without relying on emailed PDFs.</li>
            </ul>
        </div>
    </section>

</x-layouts.public-onboarding>