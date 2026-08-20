@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-6xl rounded-3xl border border-slate-200 bg-white/90 p-6 shadow-2xl backdrop-blur md:p-8">
    <div class="mb-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">{{ $ticket->ticket_number }}</h1>
            <p class="text-slate-600">{{ $ticket->title }}</p>
        </div>
        <a href="{{ route('tickets.index') }}" class="inline-flex items-center justify-center rounded-full border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100">Back</a>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <ul class="list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6">
            <h2 class="mb-4 text-xl font-semibold text-slate-900">Ticket Information</h2>
            <div class="space-y-3 text-slate-700">
                <p><strong>Submitted By:</strong> {{ $ticket->user?->name ?? 'Unknown employee' }}</p>
                <p><strong>Department:</strong> {{ $ticket->department?->name ?? 'Unassigned' }}</p>
                <p><strong>Priority:</strong> {{ ucfirst($ticket->priority) }}</p>
                <p><strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</p>
                <p><strong>Description:</strong> {{ $ticket->description }}</p>
                @if ($ticket->attachment_path)
                    <div class="pt-2">
                        <strong>Attachment:</strong>
                        <a href="{{ route('tickets.download', $ticket) }}" class="inline-flex items-center gap-1.5 text-blue-600 underline hover:text-blue-800">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Download attachment
                        </a>
                    </div>
                @endif
            </div>
        </div>

        @can('manage-tickets')
            <div class="rounded-2xl border border-slate-200 bg-white p-6">
                <h2 class="mb-4 text-xl font-semibold text-slate-900">IT Support Action</h2>
                <form action="{{ route('tickets.update', $ticket) }}" method="POST" class="space-y-3" data-confirm="Are you sure you want to save these changes?">
                    @csrf
                    @method('PUT')
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Status</label>
                            <select name="status" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                                <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Priority</label>
                            <select name="priority" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                <option value="low" {{ $ticket->priority === 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ $ticket->priority === 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ $ticket->priority === 'high' ? 'selected' : '' }}>High</option>
                                <option value="urgent" {{ $ticket->priority === 'urgent' ? 'selected' : '' }}>Urgent</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Assign To</label>
                        <select name="assigned_to" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                            <option value="">Unassigned</option>
                            @foreach (\App\Models\User::query()->whereIn('role', ['it_manager', 'it_officer'])->orderBy('name')->get() as $assignee)
                                <option value="{{ $assignee->id }}" {{ $ticket->assigned_to == $assignee->id ? 'selected' : '' }}>{{ $assignee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Response / Solution</label>
                        <textarea name="response" rows="4" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" placeholder="Add troubleshooting, solution, or close-out details..."></textarea>
                    </div>
                    <button type="submit" class="rounded-full bg-blue-600 px-5 py-2 text-sm font-medium text-white transition hover:bg-blue-700">Save Response</button>
                </form>
            </div>
        @endcan
    </div>

    <div class="mt-6 rounded-2xl border border-slate-200 bg-white p-6">
        <h2 class="mb-1 text-xl font-semibold text-slate-900">Conversation with IT Department</h2>
        <p class="mb-4 text-sm text-slate-600">Messages from you appear on the right; messages from the other participant appear on the left.</p>
        <div class="max-h-[32rem] space-y-3 overflow-y-auto rounded-xl bg-slate-50 p-4">
            @forelse ($ticket->histories as $history)
                @if (in_array($history->action, ['created', 'replied', 'responded'], true))
                    @php($isCurrentUserMessage = $history->user_id === auth()->id())
                    <div class="flex {{ $isCurrentUserMessage ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[85%] rounded-2xl px-4 py-3 shadow-sm {{ $isCurrentUserMessage ? 'rounded-br-md bg-blue-600 text-white' : 'rounded-bl-md bg-white text-slate-800' }}">
                            <p class="mb-1 text-xs font-semibold {{ $isCurrentUserMessage ? 'text-blue-100' : 'text-slate-500' }}">{{ $isCurrentUserMessage ? 'You' : ($history->user?->name ?? 'IT Support') }}</p>
                            <p class="whitespace-pre-wrap text-sm">{{ $history->details }}</p>
                            <p class="mt-2 text-right text-xs {{ $isCurrentUserMessage ? 'text-blue-100' : 'text-slate-500' }}">{{ $history->created_at?->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                @else
                    <div class="py-1 text-center text-xs text-slate-500">
                        {{ ucfirst(str_replace('_', ' ', $history->action)) }}: {{ $history->details }}
                    </div>
                @endif
            @empty
                <p class="text-center text-slate-600">No messages have been recorded yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
