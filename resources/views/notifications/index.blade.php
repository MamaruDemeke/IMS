@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-5xl space-y-6">
    <div class="rounded-3xl border border-slate-200 bg-white/90 p-6 shadow-xl backdrop-blur">
        <h1 class="text-2xl font-bold">Unread Notifications</h1>
        <p class="mt-1 text-slate-600">Open a notification to jump to the related ticket conversation.</p>
    </div>

    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl">
        @forelse ($notifications as $notification)
            <div class="border-b border-slate-200 p-4">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="font-semibold text-slate-900">{{ $notification->ticket?->title ?? 'Ticket update' }}</p>
                        <p class="text-sm text-slate-700">{{ $notification->details }}</p>
                    </div>
                    <a href="{{ route('notifications.open', $notification) }}" class="rounded-full bg-blue-600 px-3 py-2 text-sm font-medium text-white transition hover:bg-blue-700">Open</a>
                </div>
                <div class="mt-2 text-xs text-slate-500">
                    <span>{{ $notification->ticket?->ticket_number ?? 'N/A' }}</span>
                    <span class="mx-2">•</span>
                    <span>{{ ucfirst($notification->action) }}</span>
                    <span class="mx-2">•</span>
                    <span>{{ $notification->created_at?->format('M d, Y h:i A') }}</span>
                </div>
            </div>
        @empty
            <div class="p-4 text-slate-600">You have no unread notifications right now.</div>
        @endforelse
    </div>
</div>
@endsection
