@props([
    'title' => 'OnboardingFlow',
    'heading' => 'OnboardingFlow',
    'subheading' => null,
])

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <main class="min-h-screen">
        <header class="border-b border-slate-200 bg-white">
            <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-teal-700 text-lg font-bold text-white">
                        OF
                    </div>

                    <div>
                        <div class="text-sm font-semibold uppercase tracking-wide text-teal-700">
                            OnboardingFlow
                        </div>

                        <h1 class="mt-1 text-2xl font-bold text-slate-900">
                            {{ $heading }}
                        </h1>

                        @if ($subheading)
                            <p class="mt-2 max-w-3xl text-sm text-slate-600">
                                {{ $subheading }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </header>

        <section class="px-4 py-8 sm:px-6 lg:px-8">
            {{ $slot }}
        </section>
    </main>
</body>
</html>