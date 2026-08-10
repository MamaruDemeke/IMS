<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50">
<div class="container mx-auto p-6 max-w-2xl">
    <h1 class="text-2xl font-bold mb-6">Create User</h1>

    <form action="/admin/users" method="POST" class="bg-white rounded-lg shadow p-6 space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-slate-700">Name</label>
            <input type="text" name="name" class="w-full border rounded px-3 py-2" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Email</label>
            <input type="email" name="email" class="w-full border rounded px-3 py-2" required>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700">Password</label>
                <input type="password" name="password" class="w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Confirm Password</label>
                <input type="password" name="password_confirmation" class="w-full border rounded px-3 py-2" required>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700">Role</label>
                <select name="role" class="w-full border rounded px-3 py-2" required>
                    <option value="admin">Admin</option>
                    <option value="it_manager">IT Manager</option>
                    <option value="it_officer">IT Officer</option>
                    <option value="employee">Employee</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Department</label>
                <select name="department_id" class="w-full border rounded px-3 py-2" required>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Status</label>
            <select name="is_active" class="w-full border rounded px-3 py-2" required>
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Save User</button>
            <a href="/admin/users" class="bg-slate-200 px-4 py-2 rounded">Cancel</a>
        </div>
    </form>
</div>
</body>
</html>
