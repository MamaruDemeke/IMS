<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket History</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50">
<div class="container mx-auto p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">Ticket History Search</h1>
            <p class="text-slate-600">Audit and search all ticket actions by the requested criteria.</p>
        </div>
        <a href="/dashboard" class="bg-slate-800 text-white px-4 py-2 rounded">Back to Dashboard</a>
    </div>

    <form method="GET" action="/ticket-histories" class="bg-white rounded-lg shadow p-4 mb-6 grid grid-cols-1 md:grid-cols-6 gap-3">
        <div>
            <label class="block text-sm font-medium text-slate-700">Date</label>
            <input type="date" name="date" value="{{ request('date') }}" class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">From</label>
            <input type="date" name="from_date" value="{{ request('from_date') }}" class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">To</label>
            <input type="date" name="to_date" value="{{ request('to_date') }}" class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Employee</label>
            <select name="employee" class="w-full border rounded px-3 py-2">
                <option value="">All</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->name }}" {{ request('employee') === $employee->name ? 'selected' : '' }}>{{ $employee->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Department</label>
            <select name="department_id" class="w-full border rounded px-3 py-2">
                <option value="">All</option>
                @foreach($departments as $department)
                    <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Priority</label>
            <select name="priority" class="w-full border rounded px-3 py-2">
                <option value="">All</option>
                <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Status</label>
            <select name="status" class="w-full border rounded px-3 py-2">
                <option value="">All</option>
                <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">IT Officer</label>
            <select name="it_officer" class="w-full border rounded px-3 py-2">
                <option value="">All</option>
                @foreach($officers as $officer)
                    <option value="{{ $officer->name }}" {{ request('it_officer') === $officer->name ? 'selected' : '' }}>{{ $officer->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Search</button>
            <a href="/ticket-histories" class="bg-slate-200 px-4 py-2 rounded">Clear</a>
        </div>
    </form>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full text-left">
            <thead class="bg-slate-100">
            <tr>
                <th class="p-3">Ticket</th>
                <th class="p-3">Employee</th>
                <th class="p-3">Department</th>
                <th class="p-3">Priority</th>
                <th class="p-3">Status</th>
                <th class="p-3">Action</th>
                <th class="p-3">IT Officer</th>
                <th class="p-3">Date</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($histories as $history)
                <tr class="border-t">
                    <td class="p-3">{{ $history->ticket?->ticket_number }}</td>
                    <td class="p-3">{{ $history->ticket?->user?->name ?? 'Unknown' }}</td>
                    <td class="p-3">{{ $history->ticket?->department?->name ?? 'Unassigned' }}</td>
                    <td class="p-3">{{ ucfirst($history->ticket?->priority ?? 'unknown') }}</td>
                    <td class="p-3">{{ ucfirst($history->ticket?->status ?? 'unknown') }}</td>
                    <td class="p-3">{{ ucfirst($history->action) }}</td>
                    <td class="p-3">{{ $history->user?->name ?? 'System' }}</td>
                    <td class="p-3">{{ $history->created_at?->format('M d, Y h:i A') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="p-3">No ticket history records found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
