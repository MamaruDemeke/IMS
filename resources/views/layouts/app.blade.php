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
    $unreadNotifications = auth()->user()?->unreadNotifications()->count() ?? 0;
    $staffChatEnabled = \App\Models\AdminSetting::query()->where('key', 'staff_chat_enabled')->value('value') !== '0';
    $unreadChatCount = 0;
    if ($staffChatEnabled && auth()->check()) {
        $unreadChatCount = \App\Models\GeneralMessage::query()->where('receiver_id', auth()->id())->whereNull('read_at')->count();
    }
    $navigationButtonClass = 'rounded-full border px-3 py-2 text-sm font-medium shadow-sm transition';
    $activeNavigationButtonClass = 'border-indigo-600 bg-indigo-600 text-white shadow-indigo-200';
    $inactiveNavigationButtonClass = 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100';
@endphp
<div class="flex min-h-screen flex-col">
    <header class="sticky top-0 z-50 border-b border-slate-200/70 bg-white/90 backdrop-blur">
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
                @auth
                <a href="{{ route('profile.edit') }}" class="ml-2 flex items-center gap-2.5 rounded-full border {{ request()->routeIs('profile.*') ? 'border-indigo-500 bg-indigo-50' : 'border-slate-200 bg-white hover:border-indigo-300' }} pl-1.5 pr-3 py-1.5 transition" title="My Profile">
                    <img src="{{ auth()->user()->profile_photo_url }}" alt="" class="h-10 w-10 rounded-full border-2 border-slate-200 object-cover shadow-md">
                    <span class="text-sm font-medium text-slate-700">{{ auth()->user()->name }}</span>
                </a>
                @endauth
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="{{ $navigationButtonClass }} {{ request()->routeIs('dashboard') ? $activeNavigationButtonClass : $inactiveNavigationButtonClass }}">Dashboard</a>
                    <a href="{{ route('tickets.index') }}" class="{{ $navigationButtonClass }} {{ request()->routeIs('tickets.index', 'tickets.show', 'tickets.edit') ? $activeNavigationButtonClass : $inactiveNavigationButtonClass }}">Tickets</a>
                    @unless (auth()->user()?->can('manage-tickets'))
                    <a href="{{ route('tickets.create') }}" class="{{ $navigationButtonClass }} {{ request()->routeIs('tickets.create') ? $activeNavigationButtonClass : $inactiveNavigationButtonClass }}">Create Ticket</a>
                    @endunless
                    <a href="{{ route('ticket-histories.index') }}" class="{{ $navigationButtonClass }} {{ request()->routeIs('ticket-histories.*') ? $activeNavigationButtonClass : $inactiveNavigationButtonClass }}">History</a>
                    <a href="{{ route('notifications.index') }}" class="inline-flex items-center gap-2 {{ $navigationButtonClass }} {{ request()->routeIs('notifications.*') ? $activeNavigationButtonClass : 'border-slate-900 bg-slate-900 text-white hover:bg-slate-800' }}">
                        <span>Notifications</span>
                        <span class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1 text-[11px] font-semibold">
                            {{ $unreadNotifications }}
                        </span>
                    </a>
                    @if ($staffChatEnabled)
                    <a href="{{ route('it-communications.index') }}" class="inline-flex items-center gap-2 {{ $navigationButtonClass }} {{ request()->routeIs('it-communications.*') ? $activeNavigationButtonClass : $inactiveNavigationButtonClass }}" title="Staff chat" aria-label="Staff chat">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.78 9.78 0 01-4.255-.949L3 20l1.325-3.535A7.725 7.725 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <span>Staff Chat</span>
                        <span id="chat-unread-badge" class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1 text-[11px] font-semibold" @if($unreadChatCount === 0) style="display:none" @endif>
                            {{ $unreadChatCount }}
                        </span>
                    </a>
                    @endif
                    @if (auth()->user()?->role === 'admin')
                        <a href="{{ route('admin.users.index') }}" class="{{ $navigationButtonClass }} {{ request()->routeIs('admin.users.*') ? $activeNavigationButtonClass : $inactiveNavigationButtonClass }}">Users</a>
                        <a href="{{ route('admin.settings.index') }}" class="{{ $navigationButtonClass }} {{ request()->routeIs('admin.settings.*') ? $activeNavigationButtonClass : $inactiveNavigationButtonClass }}">Settings</a>
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

    <main class="flex-1 px-4 py-6 pb-24 sm:px-6 lg:px-8">
        @yield('content')
    </main>

    <footer class="fixed inset-x-0 bottom-0 z-50 border-t border-slate-200/70 bg-white/90 backdrop-blur">
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

        var chatBadge = document.getElementById('chat-unread-badge');
        if (chatBadge) {
            setInterval(function () {
                fetch('{{ route("it-communications.unread-count") }}', {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.total_unread > 0) {
                        chatBadge.textContent = data.total_unread;
                        chatBadge.style.display = '';
                    } else {
                        chatBadge.style.display = 'none';
                    }
                })
                .catch(function () {});
            }, 10000);
        }
    });
</script>
</body>
</html>

