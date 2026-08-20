@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl space-y-6">
    <div class="flex items-center gap-4 rounded-3xl border border-slate-200 bg-white/90 p-6 shadow-xl backdrop-blur">
        <a href="{{ route('profile.edit') }}">
            <img src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}" class="h-16 w-16 rounded-full border-2 border-slate-200 object-cover shadow-md transition hover:border-indigo-400">
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Welcome, {{ auth()->user()->name }}</h1>
            <p class="text-sm text-slate-600">{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }} &middot; {{ auth()->user()->department?->name ?? 'No department' }}</p>
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
        <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
            <div>
                <h2 class="text-xl font-semibold">Recent Notifications</h2>
                <p class="text-sm text-slate-600">Your ticket updates from the last 3 days.</p>
            </div>
            <a href="{{ route('notifications.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">View unread</a>
        </div>

        <div class="max-h-96 overflow-y-auto pr-2">
            @forelse ($recentNotifications as $notification)
                <div class="mb-4 border-b border-slate-200 pb-4 last:mb-0 last:border-b-0 last:pb-0">
                    <div class="mb-2 flex items-center justify-between gap-3">
                        <div>
                            <p class="font-semibold text-slate-900">{{ $notification->data['ticket_title'] ?? 'Ticket update' }}</p>
                            <p class="text-sm text-slate-600">{{ ucfirst($notification->data['action'] ?? 'updated') }}</p>
                        </div>
                        @if ($notification->read_at === null)
                            <span class="rounded-full bg-blue-100 px-2 py-1 text-xs font-semibold uppercase text-blue-700">New</span>
                        @endif
                    </div>

                    <p class="mb-2 text-sm text-slate-700">{{ $notification->data['details'] ?? 'No details available.' }}</p>

                    <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500">
                        <span>{{ $notification->data['ticket_number'] ?? 'N/A' }}</span>
                        <a href="{{ route('notifications.open', $notification) }}" class="font-medium text-blue-600 hover:text-blue-700">Open ticket</a>
                        <span>{{ $notification->created_at?->format('M d, Y h:i A') }}</span>
                    </div>
                </div>
            @empty
                <p class="text-slate-600">No notifications from the last 3 days.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
