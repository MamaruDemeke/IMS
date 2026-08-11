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
            </div>
        </div>

        @can('respond-to-ticket', $ticket)
            <div class="rounded-2xl border border-slate-200 bg-white p-6">
                <h2 class="mb-4 text-xl font-semibold text-slate-900">{{ auth()->user()?->can('manage-tickets') ? 'Add Conversation' : 'Reply to IT Department' }}</h2>
                <form action="{{ route('tickets.messages.store', $ticket) }}" method="POST" class="space-y-4" enctype="multipart/form-data" data-confirm="Are you sure you want to send this message?">
                    @csrf
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Message</label>
                        <textarea name="message" rows="5" class="w-full rounded-xl border border-slate-300 px-3 py-2.5" placeholder="Describe the issue, follow-up details, or your latest update..."></textarea>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Attachment (optional)</label>
                        <input type="file" name="attachment" class="w-full rounded-xl border border-dashed border-slate-300 bg-slate-50 px-3 py-2.5 text-sm" accept=".pdf,.jpg,.jpeg,.png,.txt,.doc,.docx">
                    </div>
                    <button type="submit" class="rounded-full bg-sky-600 px-5 py-2.5 font-medium text-white transition hover:bg-sky-700">{{ auth()->user()?->can('manage-tickets') ? 'Send Message' : 'Reply' }}</button>
                </form>
            </div>
        @endcan
    </div>

    @can('manage-tickets')
        <div class="mt-6 rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="mb-4 text-xl font-semibold text-slate-900">IT Support Action</h2>
            <form action="{{ route('tickets.update', $ticket) }}" method="POST" class="space-y-4" data-confirm="Are you sure you want to save these changes?">
                @csrf
                @method('PUT')
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Status</label>
                    <select name="status" class="w-full rounded-xl border border-slate-300 px-3 py-2.5">
                        <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                        <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Priority</label>
                    <select name="priority" class="w-full rounded-xl border border-slate-300 px-3 py-2.5">
                        <option value="low" {{ $ticket->priority === 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ $ticket->priority === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ $ticket->priority === 'high' ? 'selected' : '' }}>High</option>
                        <option value="urgent" {{ $ticket->priority === 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Assign To</label>
                    <select name="assigned_to" class="w-full rounded-xl border border-slate-300 px-3 py-2.5">
                        <option value="">Unassigned</option>
                        @foreach (\App\Models\User::query()->whereIn('role', ['it_manager', 'it_officer'])->orderBy('name')->get() as $assignee)
                            <option value="{{ $assignee->id }}" {{ $ticket->assigned_to == $assignee->id ? 'selected' : '' }}>{{ $assignee->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Response / Solution</label>
                    <textarea name="response" rows="5" class="w-full rounded-xl border border-slate-300 px-3 py-2.5" placeholder="Add troubleshooting, solution, or close-out details..."></textarea>
                </div>
                <button type="submit" class="rounded-full bg-blue-600 px-5 py-2.5 font-medium text-white transition hover:bg-blue-700">Save Response</button>
            </form>
        </div>
    @endcan

    <div class="mt-6 rounded-2xl border border-slate-200 bg-white p-6">
        <h2 class="mb-4 text-xl font-semibold text-slate-900">Ticket Conversation Timeline</h2>
        <div class="space-y-3">
            @forelse ($ticket->histories as $history)
                <div class="border-b border-slate-200 pb-3 last:border-b-0">
                    <p class="font-semibold text-slate-900">{{ ucfirst(str_replace('_', ' ', $history->action)) }} by {{ $history->user?->name ?? 'System' }}</p>
                    <p class="text-slate-700">{{ $history->details }}</p>
                    <p class="text-xs text-slate-500">{{ $history->created_at?->format('M d, Y h:i A') }}</p>
                </div>
            @empty
                <p class="text-slate-600">No history has been recorded yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
