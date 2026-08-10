<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ITSMS Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50">
<div class="container mx-auto p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-semibold text-slate-900">Yegna Trading PLC - ITSMS</h1>
            <p class="text-slate-600">Enterprise dashboard for service management and operations</p>
        </div>
        <div class="flex items-center gap-3">
            <dv">
                <spanclass="
                    <spanclass="
                        <span class="inline-flex items-center gap-2 rounded-full bg-slate-900 px-3 py-2 text-white">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0a3 3 0 11-6 0m6 0H9" />
                  sp n>
            </div         </svg>
                  sp n>
            </div     <span class="text-xs font-semibold">{{ $communicationNotifications }}</span>
                </span>
            </div>
            <a href="/tickets" class="bg-blue-600 text-white px-4 py-2 rounded">Tickets</a>
            <a href="/tickets/create" class="bg-slate-800 text-white px-4 py-2 rounded">Create Ticket</a>
            @if (auth()->user()?->role === 'admin')
                <a href="/admin/users" class="bg-emerald-600 text-white px-4 py-2 rounded">User Management</a>
                <a href="/admin/settings" class="bg-amber-600 text-white px-4 py-2 rounded">Settings</a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-slate-500">{{ $canManageTickets ? 'Total Tickets' : 'My Tickets' }}</div>
            <div class="text-2xl font-bold">{{ $tickets }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-slate-500">{{ $canManageTickets ? 'Open Tickets' : 'My Open Tickets' }}</div>
            <div class="text-2xl font-bold">{{ $openTickets }}</div>
        </div>
        @if ($canManageTickets)
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-slate-500">Users</div>
                <div class="text-2xl font-bold">{{ $users }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-slate-500">Departments</div>
                <div class="text-2xl font-bold">{{ $departments }}</div>
            </div>
        @endif
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold mb-4">{{ $canManageTickets ? 'Recent Notifications' : 'My Ticket Activity' }}</h2>

        @forelse ($recentNotifications as $notification)
            <div class="border-b border-slate-200 pb-4 mb-4 last:border-b-0 last:pb-0 last:mb-0">
                <div class="flex items-center justify-between gap-3 mb-2">
                    <div>
                        <p class="font-semibold text-slate-900">{{ $notification->user?->name ?? 'System' }}</p>
                        <p class="text-sm text-slate-600">{{ ucfirst($notification->action) }}</p>
                    </div>
                    <span class="text-xs font-semibold uppercase rounded-full bg-slate-100 px-2 py-1 text-slate-700">
                        {{ $notification->ticket?->priority ?? 'unknown' }}
                    </span>
                </div>

                <p class="text-sm text-slate-700 mb-2">{{ $notification->details }}</p>

                <div class="flex items-center gap-3 text-xs text-slate-500">
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
</body>
</html>
