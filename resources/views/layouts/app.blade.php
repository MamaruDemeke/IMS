<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yegna Trading PLC - ITSMS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-100 via-sky-50 to-indigo-100 text-slate-800">
@php
    $unreadNotifications = auth()->check()
        ? \App\Models\TicketHistory::query()
            ->where('is_read', false)
            ->whereHas('ticket', fn ($query) => $query->where('user_id', auth()->id()))
            ->count()
        : 0;
@endphp
<div class="flex min-h-screen flex-col">
    <header class="border-b border-slate-200/70 bg-white/90 backdrop-blur">
        <div class="mx-auto flex max-w-7xl flex-col gap-3 px-4 py-4 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
            <div class="flex items-center gap-3">
                <a href="#" onclick="event.preventDefault(); window.history.length > 1 ? window.history.back() : window.location.href='{{ route('login') }}';" class="inline-flex items-center gap-2 rounded-full border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-100" title="Back">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span>Back</span>
                </a>
                <div>
                    <h1 class="text-lg font-semibold text-slate-900">Yegna Trading PLC</h1>
                    <p class="text-sm text-slate-600">IT Service Management</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="rounded-full border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-100">Dashboard</a>
                    <a href="{{ route('tickets.index') }}" class="rounded-full border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-100">Tickets</a>
                    <a href="{{ route('ticket-histories.index') }}" class="rounded-full border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-100">History</a>
                    <a href="{{ route('notifications.index') }}" class="inline-flex items-center gap-2 rounded-full bg-slate-900 px-3 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-slate-800">
                        <span>Notifications</span>
                        <span class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1 text-[11px] font-semibold">
                            {{ $unreadNotifications }}
                        </span>
                    </a>
                    @if (auth()->user()?->role === 'admin')
                        <a href="{{ route('admin.users.index') }}" class="rounded-full border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-100">Users</a>
                        <a href="{{ route('admin.settings.index') }}" class="rounded-full border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-100">Settings</a>
                    @endif
                    <a href="#" onclick="event.preventDefault(); if (confirm('Are you sure you want to exit the application?')) { document.getElementById('logout-form').submit(); }" class="inline-flex items-center gap-2 rounded-full border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-100" title="Exit">
                        <span>Exit</span>
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v8a2 2 0 002 2h4M16 7l4 4m0 0l-4 4m4-4H10" />
                        </svg>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>
                @else
                    <a href="{{ route('login') }}" class="rounded-full border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-100">Login</a>
                @endauth
            </div>
        </div>
    </header>

    <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
        @yield('content')
    </main>

    <footer class="border-t border-slate-200/70 bg-white/80 backdrop-blur">
        <div class="mx-auto flex max-w-7xl flex-col gap-2 px-4 py-4 text-sm text-slate-600 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
            <p>© {{ date('Y') }} Yegna Trading PLC. IT Service Management.</p>
            <p>Secure, reliable, and responsive support operations.</p>
        </div>
    </footer>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('form[data-confirm]').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                const message = form.getAttribute('data-confirm');
                if (message && !confirm(message)) {
                    event.preventDefault();
                }
            });
        });

        document.querySelectorAll('a[data-confirm]').forEach(function (link) {
            link.addEventListener('click', function (event) {
                const message = link.getAttribute('data-confirm');
                if (message && !confirm(message)) {
                    event.preventDefault();
                }
            });
        });
    });
</script>
</body>
</html>

