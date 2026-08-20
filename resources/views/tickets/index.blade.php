@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl space-y-6">
    <div class="flex flex-col gap-4 rounded-3xl border border-slate-200 bg-white/90 p-6 shadow-xl backdrop-blur md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold">Ticket Queue</h1>
            @unless (auth()->user()?->can('manage-tickets'))
                <p class="mt-1 text-sm text-slate-600">You can only see the tickets you submitted. The IT department can view and respond to all tickets.</p>
            @endunless
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('ticket-histories.index') }}" class="rounded-full bg-violet-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-violet-700">History</a>
            @unless (auth()->user()?->can('manage-tickets'))
            <a href="{{ route('tickets.create') }}" class="rounded-full bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-blue-700">New Ticket</a>
            @endunless
        </div>
    </div>

    @if (session('status'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('status') }}</div>
    @endif

    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl">
        <table class="min-w-full text-left">
            <thead class="bg-slate-100">
            <tr>
                <th class="p-3">Ticket</th>
                <th class="p-3">Submitted By</th>
                <th class="p-3">Title</th>
                <th class="p-3">Priority</th>
                <th class="p-3">Status</th>
                <th class="p-3">Department</th>
                <th class="p-3">Communication</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($tickets as $ticket)
                <tr class="border-t">
                    <td class="p-3"><a href="{{ route('tickets.show', $ticket) }}" class="text-blue-600 underline">{{ $ticket->ticket_number }}</a></td>
                    <td class="p-3">{{ $ticket->user?->name ?? 'Unknown employee' }}</td>
                    <td class="p-3">{{ $ticket->title }}</td>
                    <td class="p-3">{{ ucfirst($ticket->priority) }}</td>
                    <td class="p-3">{{ ucfirst($ticket->status) }}</td>
                    <td class="p-3">{{ $ticket->department?->name ?? 'Unassigned' }}</td>
                    <td class="p-3">
                        @can('manage-tickets')
                            <a href="{{ route('tickets.show', $ticket) }}?compose=1#ticket-message" class="inline-flex rounded-full bg-sky-600 px-3 py-2 text-sm font-medium text-white transition hover:bg-sky-700">
                                Secure IT Communication
                            </a>
                        @endcan
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="p-3" colspan="7">No tickets have been submitted yet.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
