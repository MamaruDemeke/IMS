@extends('layouts.app')

@section('content')
<div class="flex min-h-full items-center justify-center px-4 py-8">
    <div class="w-full max-w-md rounded-2xl border border-slate-200 bg-white p-8 shadow-xl">
        <div class="mb-8 text-center">
            <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-indigo-100 text-indigo-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM4 20a8 8 0 0116 0" />
                </svg>
            </div>
            <h1 class="text-2xl font-semibold text-slate-900">Yegna Trading PLC</h1>
            <p class="mt-2 text-sm text-slate-500">Sign in to your account</p>
        </div>

        @if ($errors->any())
            <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <p class="font-medium">Please fix the following issues:</p>
                <ul class="mt-2 list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('final_login_attempt_available_at'))
            <div id="final-login-countdown" data-available-at="{{ session('final_login_attempt_available_at') }}" class="mb-5 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                Your final login attempt will be available in <span class="font-semibold">1:00</span>.
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf
            <div>
                <label for="email" class="mb-2 block text-sm font-medium text-slate-700">Email</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="you@example.com"
                    autocomplete="email"
                    required
                    class="w-full rounded-lg border px-3 py-2.5 text-sm text-slate-900 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 @error('email') border-red-500 focus:border-red-500 focus:ring-red-200 @else border-slate-300 @enderror"
                >
                @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="mb-2 block text-sm font-medium text-slate-700">Password</label>
                <div class="relative">
                    <input
                        id="password"
                        type="password"
                        name="password"
                        placeholder="Enter your password"
                        autocomplete="current-password"
                        required
                        minlength="8"
                        class="w-full rounded-lg border px-3 py-2.5 pr-11 text-sm text-slate-900 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 @error('password') border-red-500 focus:border-red-500 focus:ring-red-200 @else border-slate-300 @enderror"
                    >
                    <button
                        type="button"
                        id="togglePassword"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-500 hover:text-slate-700"
                        aria-label="Show password"
                    >
                        <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12z" />
                            <circle cx="12" cy="12" r="3" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" />
                        </svg>
                        <svg id="eyeSlashIcon" xmlns="http://www.w3.org/2000/svg" class="hidden h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3l18 18M10.5 10.5A3 3 0 0012 15a3 3 0 001.5-5.5M6.9 6.9C4.1 8.6 2.25 12 2.25 12s3.75 6.75 9.75 6.75c1.5 0 2.9-.3 4.15-.83M17.1 17.1c1.7-1.7 2.9-4.1 2.9-5.1 0-1.25-1.1-3.2-3.4-4.7" />
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-xs text-slate-500">Use at least 8 characters.</p>
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center space-x-2 text-sm text-slate-600">
                    <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" {{ old('remember') ? 'checked' : '' }}>
                    <span>Remember me</span>
                </label>
            </div>

            <button type="submit" class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 font-medium text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Sign In
            </button>
        </form>
    </div>
</div>

<script>
    const passwordInput = document.getElementById('password');
    const togglePassword = document.getElementById('togglePassword');
    const eyeIcon = document.getElementById('eyeIcon');
    const eyeSlashIcon = document.getElementById('eyeSlashIcon');

    togglePassword?.addEventListener('click', () => {
        const isPassword = passwordInput.type === 'password';
        passwordInput.type = isPassword ? 'text' : 'password';
        togglePassword.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
        eyeIcon.classList.toggle('hidden', !isPassword);
        eyeSlashIcon.classList.toggle('hidden', isPassword);
    });

    const countdown = document.getElementById('final-login-countdown');

    if (countdown) {
        const availableAt = new Date(countdown.dataset.availableAt);
        const countdownText = countdown.querySelector('span');
        let countdownInterval;
        const updateCountdown = () => {
            const secondsRemaining = Math.max(0, Math.ceil((availableAt.getTime() - Date.now()) / 1000));
            const minutes = Math.floor(secondsRemaining / 60);
            const seconds = String(secondsRemaining % 60).padStart(2, '0');

            countdownText.textContent = `${minutes}:${seconds}`;

            if (secondsRemaining === 0) {
                countdownText.textContent = 'now';
                clearInterval(countdownInterval);
            }
        };

        updateCountdown();
        countdownInterval = setInterval(updateCountdown, 1000);
    }
</script>
@endsection
                <
