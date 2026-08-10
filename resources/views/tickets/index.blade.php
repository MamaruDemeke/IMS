<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Queue</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50">
<div class="container mx-auto p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">Ticket Queue</h1>
            @unless (auth()->user()?->can('manage-tickets'))
                <p class="text-sm text-slate-600 mt-1">You can only see the tickets you submitted. The IT department can view and respond to all tickets.</p>
            @endunless
        </div>
        <a href="/tickets/create" class="bg-blue-600 text-white px-4 py-2 rounded">New Ticket</a>
    </div>

    @if (session('status'))
        <div class="bg-green-100 text-green-800 px-4 py-3 rounded mb-4">{{ session('status') }}</div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full text-left">
            <thead class="bg-slate-100">
            <tr>
                <th class="p-3">Ticket</th>
                <th class="p-3">Submitted By</th>
                <th class="p-3">Title</th>
                <th class="p-3">Priority</th>
                <th class="p-3">Status</th>
                <th class="p-3">Department</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($tickets as $ticket)
                <tr class="border-t">
                    <td class="p-3"><a href="/tickets/{{ $ticket->id }}" class="text-blue-600 underline">{{ $ticket->ticket_number }}</a></td>
                    <td class="p-3">{{ $ticket->user?->name ?? 'Unknown employee' }}</td>
                    <td class="p-3">{{ $ticket->title }}</td>
                    <td class="p-3">{{ ucfirst($ticket->priority) }}</td>
                    <td class="p-3">{{ ucfirst($ticket->status) }}</td>
                    <td class="p-3">{{ $ticket->department?->name ?? 'Unassigned' }}</td>
                </tr>
            @empty
                <tr>
                    <td class="p-3" colspan="6">No tickets have been submitted yet.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
