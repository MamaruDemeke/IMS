<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Ticket</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50">
<div class="container mx-auto p-6 max-w-2xl">
    <h1 class="text-2xl font-bold mb-6">Create IT Support Ticket</h1>

    <form action="/tickets" method="POST" class="bg-white rounded-lg shadow p-6 space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-slate-700">Submitted By</label>
            <input type="text" value="{{ auth()->user()?->name ?? 'Unknown employee' }}" class="w-full border rounded px-3 py-2 bg-slate-100" readonly>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Title</label>
            <input type="text" name="title" class="w-full border rounded px-3 py-2" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Description</label>
            <textarea name="description" class="w-full border rounded px-3 py-2" rows="5" required></textarea>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700">Priority</label>
                <select name="priority" class="w-full border rounded px-3 py-2" required>
                    <option value="low">Low</option>
                    <option value="medium" selected>Medium</option>
                    <option value="high">High</option>
                    <option value="urgent">Urgent</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Category</label>
                <input type="text" name="category" class="w-full border rounded px-3 py-2" required>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Department</label>
            <select name="department_id" class="w-full border rounded px-3 py-2" required>
                @foreach($departments as $department)
                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Submit Ticket</button>
            <a href="/tickets" class="bg-slate-200 px-4 py-2 rounded">Cancel</a>
        </div>
    </form>
</div>
</body>
</html>
