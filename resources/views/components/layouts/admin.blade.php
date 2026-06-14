<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'OnboardingFlow' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <div class="flex min-h-screen">
        <aside class="hidden w-72 shrink-0 border-r border-slate-200 bg-white px-5 py-6 lg:block">
            <div>
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-teal-700 text-lg font-bold text-white">
                        OF
                    </div>

                    <div>
                        <div class="text-lg font-bold text-slate-900">Onboarding Flow</div>
                        <div class="text-sm text-slate-500">Streamline. Track. Review.</div>
                    </div>
                </div>
            </div>

            @auth
                <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                        Signed in as
                    </div>

                    <div class="mt-2 text-sm font-semibold text-slate-900">
                        {{ auth()->user()->name }}
                    </div>

                    <div class="mt-1 text-xs text-slate-500">
                        {{ auth()->user()->roleLabel() }}
                    </div>

                    <form method="POST" action="{{ route('logout') }}" class="mt-3">
                        @csrf

                        <button
                            type="submit"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
                        >
                            Sign Out
                        </button>
                    </form>
                </div>
            @endauth

            @php
                $items = [
                    ['label' => 'Overview', 'route' => 'admin.onboarding.dashboard', 'roles' => ['admin', 'reviewer', 'readonly']],
                    ['label' => 'Invites', 'route' => 'admin.onboarding.invites.index', 'roles' => ['admin', 'reviewer', 'readonly']],
                    ['label' => 'Create Invite', 'route' => 'admin.onboarding.invites.create', 'roles' => ['admin']],
                    ['label' => 'Form Templates', 'route' => 'admin.onboarding.templates.index', 'roles' => ['admin', 'reviewer', 'readonly']],
                    ['label' => 'Activity Log', 'route' => 'admin.onboarding.activity-log.index', 'roles' => ['admin', 'reviewer', 'readonly']],
                    ['label' => 'Reports', 'route' => 'admin.onboarding.reports.index', 'roles' => ['admin', 'reviewer', 'readonly']],
                ];

                $currentRole = auth()->user()?->role;
            @endphp

            <nav class="mt-6 space-y-2">
                @foreach ($items as $item)
                    @if (! $currentRole || in_array($currentRole, $item['roles'], true))
                        <a
                            href="{{ route($item['route']) }}"
                            class="block rounded-xl px-4 py-2 text-sm font-medium transition
                                {{ request()->routeIs($item['route'])
                                    ? 'bg-teal-700 text-white shadow-sm'
                                    : 'text-slate-700 hover:bg-slate-100'
                                }}"
                        >
                            {{ $item['label'] }}
                        </a>
                    @endif
                @endforeach
            </nav>

            <div class="mt-8">
                <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                    Planned Later
                </div>

                <div class="mt-3 space-y-2">
                    @foreach (['Settings', 'Microsoft Login', 'SharePoint Storage'] as $placeholder)
                        <div class="rounded-xl bg-slate-50 px-4 py-2 text-sm text-slate-400">
                            {{ $placeholder }}
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-8 rounded-2xl border border-slate-200 bg-slate-50 p-4 text-xs text-slate-500">
                Version: 1.0.0 Local Proof
            </div>
        </aside>

        <main class="flex-1">
            <header class="border-b border-slate-200 bg-white px-4 py-5 sm:px-6 lg:px-8">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900">
                            {{ $heading ?? 'OnboardingFlow' }}
                        </h1>

                        @isset($subheading)
                            <p class="mt-1 max-w-3xl text-sm text-slate-600">
                                {{ $subheading }}
                            </p>
                        @endisset
                    </div>

                    @auth
                        @if (auth()->user()->isAdmin())
                            <a
                                href="{{ route('admin.onboarding.invites.create') }}"
                                class="inline-flex rounded-xl bg-teal-700 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-teal-800"
                            >
                                + Create Invite
                            </a>
                        @endif
                    @endauth
                </div>
            </header>

            <div class="px-4 py-6 sm:px-6 lg:px-8">
                @if (session('success'))
                    <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                        {{ session('error') }}
                    </div>
                @endif

                {{ $slot }}
            </div>
        </main>
    </div>
</body>
</html>