<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'OnboardingFlow' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">
    <div class="min-h-screen">
        <header class="bg-gradient-to-br from-teal-950 via-teal-900 to-teal-800 text-white">
            <div class="mx-auto max-w-5xl px-6 py-8">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-3xl font-semibold tracking-tight">
                            Onboarding<span class="text-cyan-300">Flow</span>
                        </div>
                        <div class="mt-1 text-xs uppercase tracking-[0.25em] text-cyan-100">
                            Secure onboarding workflow
                        </div>
                    </div>

                    <div class="hidden rounded-full border border-cyan-200/40 bg-white/10 px-4 py-2 text-sm text-cyan-50 md:block">
                        Local Proof
                    </div>
                </div>

                <div class="mt-10">
                    <h1 class="text-3xl font-semibold tracking-tight">{{ $heading ?? 'Complete Onboarding' }}</h1>

                    @isset($subheading)
                        <p class="mt-3 max-w-2xl text-sm leading-6 text-cyan-50">
                            {{ $subheading }}
                        </p>
                    @endisset
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-5xl px-6 py-10">
            {{ $slot }}
        </main>

        <footer class="mx-auto max-w-5xl px-6 pb-10 text-xs text-slate-400">
            OnboardingFlow proof-of-concept · Replacing emailed PDF onboarding with a trackable web workflow.
        </footer>
    </div>
</body>
</html>