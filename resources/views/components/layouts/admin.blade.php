<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'OnboardingFlow' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900">
    <div class="min-h-screen flex">
        <aside class="w-80 bg-white border-r border-slate-200 flex flex-col">
            <div class="bg-gradient-to-br from-teal-950 via-teal-900 to-teal-800 text-white px-8 py-8">
                <div class="text-3xl font-semibold tracking-tight">Onboarding<span class="text-cyan-300">Flow</span></div>
                <div class="text-xs uppercase tracking-[0.25em] text-cyan-100 mt-1">Streamline. Track. Review.</div>

                <div class="mt-8 flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-cyan-400/20 border border-cyan-300 flex items-center justify-center text-lg font-semibold">
                        CS
                    </div>
                    <div>
                        <div class="text-sm text-cyan-100">Welcome back,</div>
                        <div class="font-semibold">Codie Shannon</div>
                        <div class="text-sm text-cyan-100">Prototype Admin</div>
                    </div>
                </div>
            </div>

            <nav class="flex-1 px-5 py-6 space-y-1">
                @php
                    $items = [
                        ['label' => 'Overview', 'route' => 'admin.onboarding.dashboard'],
                        ['label' => 'Invites', 'route' => 'admin.onboarding.invites.index'],
                        ['label' => 'Create Invite', 'route' => 'admin.onboarding.invites.create'],
                        ['label' => 'Form Templates', 'route' => 'admin.onboarding.templates.index'],
                        ['label' => 'Activity Log', 'route' => 'admin.onboarding.activity-log.index'],
                    ];
                @endphp

                @foreach ($items as $item)
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition
                       {{ request()->routeIs($item['route'])
                            ? 'bg-teal-800 text-white shadow-sm'
                            : 'text-teal-950 hover:bg-teal-50' }}">
                        <span class="w-2 h-2 rounded-full {{ request()->routeIs($item['route']) ? 'bg-cyan-300' : 'bg-teal-700' }}"></span>
                        {{ $item['label'] }}
                    </a>
                @endforeach

                <div class="pt-5 mt-5 border-t border-slate-200">
                    <div class="px-4 py-2 text-xs font-semibold uppercase tracking-wider text-slate-400">Planned Week 2</div>

                    @foreach (['Review Queue', 'Missing Info', 'Reports', 'Settings'] as $placeholder)
                        <div class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-slate-400">
                            <span class="w-2 h-2 rounded-full bg-slate-300"></span>
                            {{ $placeholder }}
                        </div>
                    @endforeach
                </div>
            </nav>

            <div class="bg-teal-950 text-white px-6 py-5 flex items-center justify-between text-sm">
                <span>Version: 1.0.0</span>
                <span class="text-cyan-100">Local Proof</span>
            </div>
        </aside>

        <main class="flex-1">
            <header class="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-10">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">{{ $heading ?? 'OnboardingFlow' }}</h1>
                    @isset($subheading)
                        <p class="text-sm text-slate-500 mt-1">{{ $subheading }}</p>
                    @endisset
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.onboarding.invites.create') }}"
                       class="inline-flex items-center rounded-xl bg-teal-800 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-900">
                        + Create Invite
                    </a>
                </div>
            </header>

            <div class="p-10">
                @if (session('success'))
                    <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800">
                        {{ session('success') }}
                    </div>
                @endif

                {{ $slot }}
            </div>
        </main>
    </div>
</body>
</html>