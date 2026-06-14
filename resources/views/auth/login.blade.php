<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>OnboardingFlow Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100">
    <main class="flex min-h-screen items-center justify-center px-4 py-10">
        <section class="w-full max-w-md rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
            <div>
                <div class="inline-flex rounded-2xl bg-teal-700 px-4 py-3 text-lg font-bold text-white">
                    OF
                </div>

                <h1 class="mt-5 text-2xl font-bold text-slate-900">
                    Sign in to OnboardingFlow
                </h1>

                <p class="mt-2 text-sm text-slate-600">
                    Local demo users for admin, reviewer, and read-only access.
                </p>
            </div>

            @if ($errors->any())
                <div class="mt-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}" class="mt-6 space-y-4">
                @csrf

                <div>
                    <label for="email" class="text-sm font-medium text-slate-700">
                        Email
                    </label>

                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-200"
                    >
                </div>

                <div>
                    <label for="password" class="text-sm font-medium text-slate-700">
                        Password
                    </label>

                    <input
                        id="password"
                        name="password"
                        type="password"
                        required
                        class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-200"
                    >
                </div>

                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input
                        type="checkbox"
                        name="remember"
                        value="1"
                        class="rounded border-slate-300 text-teal-700 focus:ring-teal-500"
                    >
                    Remember me
                </label>

                <button
                    type="submit"
                    class="w-full rounded-xl bg-teal-700 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-teal-800"
                >
                    Sign In
                </button>
            </form>

            <div class="mt-6 rounded-xl bg-slate-50 p-4 text-xs text-slate-600">
                <div class="font-semibold text-slate-700">Demo users after seeding:</div>
                <div class="mt-2 space-y-1">
                    <div>admin@example.com / password</div>
                    <div>reviewer@example.com / password</div>
                    <div>readonly@example.com / password</div>
                </div>
            </div>
        </section>
    </main>
</body>
</html>