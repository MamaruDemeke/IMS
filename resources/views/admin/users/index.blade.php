@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl space-y-6">
    <div class="flex flex-col gap-4 rounded-3xl border border-slate-200 bg-white/90 p-6 shadow-xl backdrop-blur md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold">User Management</h1>
            <p class="text-slate-600">Admin controls for password reset, roles, and user CRUD.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.settings.index') }}" class="rounded-full bg-slate-800 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-700">Settings</a>
            <a href="{{ route('admin.users.create') }}" class="rounded-full bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700">Create User</a>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap items-end gap-3 rounded-3xl border border-slate-200 bg-white p-4 shadow-xl">
        <div class="min-w-48 flex-1">
            <label class="mb-2 block text-sm font-medium text-slate-700">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2" placeholder="Name or email">
        </div>
        <div class="min-w-40">
            <label class="mb-2 block text-sm font-medium text-slate-700">Role</label>
            <select name="role" class="w-full rounded-xl border border-slate-300 px-3 py-2">
                <option value="">All Roles</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="it_manager" {{ request('role') === 'it_manager' ? 'selected' : '' }}>IT Manager</option>
                <option value="it_officer" {{ request('role') === 'it_officer' ? 'selected' : '' }}>IT Officer</option>
                <option value="employee" {{ request('role') === 'employee' ? 'selected' : '' }}>Employee</option>
            </select>
        </div>
        <div class="min-w-40">
            <label class="mb-2 block text-sm font-medium text-slate-700">Department</label>
            <select name="department_id" class="w-full rounded-xl border border-slate-300 px-3 py-2">
                <option value="">All Departments</option>
                @foreach($departments as $department)
                    <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="min-w-40">
            <label class="mb-2 block text-sm font-medium text-slate-700">Status</label>
            <select name="status" class="w-full rounded-xl border border-slate-300 px-3 py-2">
                <option value="">All</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="rounded-full bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700">Filter</button>
            <a href="{{ route('admin.users.index') }}" class="rounded-full bg-slate-200 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-300">Clear</a>
        </div>
    </form>

    @if (session('status'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('status') }}</div>
    @endif

    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl">
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
                        <a href="{{ route('admin.users.edit', $user) }}" class="text-blue-600">Edit</a>
                        <button type="button" data-open-reset="reset-{{ $user->id }}" class="text-amber-600">Reset Password</button>
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" data-confirm="Delete this user? This action cannot be undone.">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600">Delete</button>
                        </form>
                    </td>
                </tr>

                <div id="reset-{{ $user->id }}" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50">
                    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
                        <div class="mb-4 flex items-center justify-between">
                            <h2 class="text-xl font-semibold">Reset Password</h2>
                            <button type="button" data-close-reset="reset-{{ $user->id }}" class="text-slate-500">Close</button>
                        </div>

                        <form action="{{ route('admin.users.reset-password', $user) }}" method="POST" class="space-y-4" data-confirm="Reset this password?">
                            @csrf
                            <div>
                                <label class="mb-2 block text-sm font-medium text-slate-700">New Password</label>
                                <input type="password" name="password" class="w-full rounded-xl border border-slate-300 px-3 py-2" required>
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium text-slate-700">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="w-full rounded-xl border border-slate-300 px-3 py-2" required>
                            </div>
                            <div class="flex gap-3">
                                <button type="submit" class="rounded-full bg-amber-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-amber-700">Confirm Reset</button>
                                <button type="button" data-close-reset="reset-{{ $user->id }}" class="rounded-full bg-slate-200 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-300">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            @empty
                <tr>
                    <td class="p-3" colspan="6">No users found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
