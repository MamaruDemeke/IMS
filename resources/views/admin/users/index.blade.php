<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50">
<div class="container mx-auto p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">User Management</h1>
            <p class="text-slate-600">Admin controls for password reset, roles, and user CRUD.</p>
        </div>
        <div class="space-x-3">
            <a href="/admin/settings" class="bg-slate-800 text-white px-4 py-2 rounded">Settings</a>
            <a href="/admin/users/create" class="bg-blue-600 text-white px-4 py-2 rounded">Create User</a>
        </div>
    </div>

    <form method="GET" action="/admin/users" class="bg-white rounded-lg shadow p-4 mb-6 flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-48">
            <label class="block text-sm font-medium text-slate-700">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" class="w-full border rounded px-3 py-2" placeholder="Name or email">
        </div>
        <div class="min-w-40">
            <label class="block text-sm font-medium text-slate-700">Role</label>
            <select name="role" class="w-full border rounded px-3 py-2">
                <option value="">All Roles</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="it_manager" {{ request('role') === 'it_manager' ? 'selected' : '' }}>IT Manager</option>
                <option value="it_officer" {{ request('role') === 'it_officer' ? 'selected' : '' }}>IT Officer</option>
                <option value="employee" {{ request('role') === 'employee' ? 'selected' : '' }}>Employee</option>
            </select>
        </div>
        <div class="min-w-40">
            <label class="block text-sm font-medium text-slate-700">Department</label>
            <select name="department_id" class="w-full border rounded px-3 py-2">
                <option value="">All Departments</option>
                @foreach($departments as $department)
                    <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="min-w-40">
            <label class="block text-sm font-medium text-slate-700">Status</label>
            <select name="status" class="w-full border rounded px-3 py-2">
                <option value="">All</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Filter</button>
            <a href="/admin/users" class="bg-slate-200 px-4 py-2 rounded">Clear</a>
        </div>
    </form>

    @if (session('status'))
        <div class="bg-green-100 text-green-800 px-4 py-3 rounded mb-4">{{ session('status') }}</div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full text-left">
            <thead class="bg-slate-100">
            <tr>
                <th class="p-3">Name</th>
                <th class="p-3">Email</th>
                <th class="p-3">Role</th>
                <th class="p-3">Department</th>
                <th class="p-3">Status</th>
                <th class="p-3">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($users as $user)
                <tr class="border-t">
                    <td class="p-3">{{ $user->name }}</td>
                    <td class="p-3">{{ $user->email }}</td>
                    <td class="p-3">{{ strtoupper(str_replace('_', ' ', $user->role)) }}</td>
                    <td class="p-3">{{ $user->department?->name ?? 'Unassigned' }}</td>
                    <td class="p-3">{{ $user->is_active ? 'Active' : 'Inactive' }}</td>
                    <td class="p-3 space-x-2">
                        <a href="/admin/users/{{ $user->id }}/edit" class="text-blue-600">Edit</a>
                        <button type="button" data-open-reset="reset-{{ $user->id }}" class="text-amber-600">Reset Password</button>
                        <form action="/admin/users/{{ $user->id }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600">Delete</button>
                        </form>
                    </td>
                </tr>

                <div id="reset-{{ $user->id }}" class="fixed inset-0 bg-slate-900/50 items-center justify-center z-50 hidden">
                    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-semibold">Reset Password</h2>
                            <button type="button" data-close-reset="reset-{{ $user->id }}" class="text-slate-500">Close</button>
                        </div>

                        <form action="/admin/users/{{ $user->id }}/reset-password" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-slate-700">New Password</label>
                                <input type="password" name="password" class="w-full border rounded px-3 py-2" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="w-full border rounded px-3 py-2" required>
                            </div>
                            <div class="flex gap-3">
                                <button type="submit" class="bg-amber-600 text-white px-4 py-2 rounded">Confirm Reset</button>
                                <button type="button" data-close-reset="reset-{{ $user->id }}" class="bg-slate-200 px-4 py-2 rounded">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
                </tr>
            @empty
                <tr>
                    <td class="p-3" colspan="6">No users found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
