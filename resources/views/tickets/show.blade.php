<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Details</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50">
<div class="container mx-auto p-6 max-w-6xl">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">{{ $ticket->ticket_number }}</h1>
            <p class="text-slate-600">{{ $ticket->title }}</p>
        </div>
        <a href="/tickets" class="bg-slate-800 text-white px-4 py-2 rounded">Back to Queue</a>
    </div>

    @if (session('status'))
        <div class="bg-green-100 text-green-800 px-4 py-3 rounded mb-4">{{ session('status') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Ticket Information</h2>
            <div class="space-y-3 text-slate-700">
                <p><strong>Submitted By:</strong> {{ $ticket->user?->name ?? 'Unknown employee' }}</p>
                <p><strong>Department:</strong> {{ $ticket->department?->name ?? 'Unassigned' }}</p>
                <p><strong>Priority:</strong> {{ ucfirst($ticket->priority) }}</p>
                <p><strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</p>
                <p><strong>Description:</strong> {{ $ticket->description }}</p>
            </div>
        </div>

        @can('respond-to-ticket', $ticket)
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Add Conversation</h2>
                <form action="{{ route('tickets.messages.store', $ticket) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Message</label>
                        <textarea name="message" rows="5" class="w-full border rounded px-3 py-2" placeholder="Describe the issue, follow-up details, or your latest update..."></textarea>
                    </div>
                    <button type="submit" class="bg-sky-600 text-white px-4 py-2 rounded">Send Message</button>
                </form>
            </div>
        @endcan
    </div>

    @can('manage-tickets')
        <div class="bg-white rounded-lg shadow p-6 mt-6">
            <h2 class="text-xl font-semibold mb-4">IT Support Action</h2>
            <form action="/tickets/{{ $ticket->id }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-slate-700">Status</label>
                    <select name="status" class="w-full border rounded px-3 py-2">
                        <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                        <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Priority</label>
                    <select name="priority" class="w-full border rounded px-3 py-2">
                        <option value="low" {{ $ticket->priority === 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ $ticket->priority === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ $ticket->priority === 'high' ? 'selected' : '' }}>High</option>
                        <option value="urgent" {{ $ticket->priority === 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Assign To</label>
                    <select name="assigned_to" class="w-full border rounded px-3 py-2">
                        <option value="">Unassigned</option>
                        @foreach (\App\Models\User::query()->whereIn('role', ['it_manager', 'it_officer'])->orderBy('name')->get() as $assignee)
                            <option value="{{ $assignee->id }}" {{ $ticket->assigned_to == $assignee->id ? 'selected' : '' }}>{{ $assignee->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Response / Solution</label>
                    <textarea name="response" rows="5" class="w-full border rounded px-3 py-2" placeholder="Add troubleshooting, solution, or close-out details..."></textarea>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Save Response</button>
            </form>
        </div>
    @endcan

    <div class="bg-white rounded-lg shadow p-6 mt-6">
        <h2 class="text-xl font-semibold mb-4">Ticket Conversation Timeline</h2>
        <div class="space-y-3">
            @forelse ($ticket->histories as $history)
                <div class="border-b pb-3 last:border-b-0">
                    <p class="font-semibold">{{ ucfirst(str_replace('_', ' ', $history->action)) }} by {{ $history->user?->name ?? 'System' }}</p>
                    <p class="text-slate-700">{{ $history->details }}</p>
                    <p class="text-xs text-slate-500">{{ $history->created_at?->format('M d, Y h:i A') }}</p>
                </div>
            @empty
                <p class="text-slate-600">No history has been recorded yet.</p>
            @endforelse
        </div>
    </div>
</div>
</body>
</html>
