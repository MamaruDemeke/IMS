@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl space-y-6">
    <div class="rounded-3xl border border-slate-200 bg-white/90 p-6 shadow-xl backdrop-blur">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-3xl font-semibold text-slate-900">Yegna Trading PLC - ITSMS</h1>
                <p class="text-slate-600">Enterprise dashboard for service management and operations</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('notifications.index') }}" class="inline-flex items-center gap-2 rounded-full bg-slate-900 px-3 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-slate-800">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0a3 3 0 11-6 0m6 0H9" />
                    </svg>
                    <span>Notifications</span>
                    <span class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1 text-[11px] font-semibold">{{ $unreadNotifications }}</span>
                </a>
                <a href="{{ route('tickets.index') }}" class="rounded-full bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-blue-700">Tickets</a>
                <a href="{{ route('ticket-histories.index') }}" class="rounded-full bg-violet-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-violet-700">Ticket History</a>
                <a href="{{ route('tickets.create') }}" class="rounded-full bg-slate-800 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-slate-700">Create Ticket</a>
                @if (auth()->user()?->role === 'admin')
                    <a href="{{ route('admin.users.index') }}" class="rounded-full bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-emerald-700">User Management</a>
                    <a href="{{ route('admin.settings.index') }}" class="rounded-full bg-amber-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-amber-700">Settings</a>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="text-slate-500">{{ $canManageTickets ? 'Total Tickets' : 'My Tickets' }}</div>
            <div class="text-2xl font-bold">{{ $tickets }}</div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="text-slate-500">{{ $canManageTickets ? 'Open Tickets' : 'My Open Tickets' }}</div>
            <div class="text-2xl font-bold">{{ $openTickets }}</div>
        </div>
        @if ($canManageTickets)
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="text-slate-500">Users</div>
                <div class="text-2xl font-bold">{{ $users }}</div>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="text-slate-500">Departments</div>
                <div class="text-2xl font-bold">{{ $departments }}</div>
            </div>
        @endif
    </div>

    <div class="rounded-3xl border border-slate-200 bg-white/90 p-6 shadow-xl backdrop-blur">
        <h2 class="mb-4 text-xl font-semibold">{{ $canManageTickets ? 'Recent Notifications' : 'My Ticket Activity' }}</h2>

        @forelse ($recentNotifications as $notification)
            <div class="border-b border-slate-200 pb-4 mb-4 last:border-b-0 last:pb-0 last:mb-0">
                <div class="mb-2 flex items-center justify-between gap-3">
                    <div>
                        <p class="font-semibold text-slate-900">{{ $notification->user?->name ?? 'System' }}</p>
                        <p class="text-sm text-slate-600">{{ ucfirst($notification->action) }}</p>
                    </div>
                    <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold uppercase text-slate-700">
                        {{ $notification->ticket?->priority ?? 'unknown' }}
                    </span>
                </div>

                <p class="mb-2 text-sm text-slate-700">{{ $notification->details }}</p>

                <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500">
                    <span>{{ $notification->ticket?->ticket_number ?? 'N/A' }}</span>
                    <span>•</span>
                    <span>{{ $notification->ticket?->department?->name ?? 'Unassigned' }}</span>
                    <span>•</span>
                    <span>{{ ucfirst($notification->ticket?->status ?? 'unknown') }}</span>
                    <span>•</span>
                    <span>{{ $notification->created_at?->format('M d, Y h:i A') }}</span>
                </div>
            </div>
        @empty
            <p class="text-slate-600">No recent notifications available.</p>
        @endforelse
    </div>
</div>
@endsection
