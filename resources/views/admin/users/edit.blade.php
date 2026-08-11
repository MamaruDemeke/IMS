@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-3xl rounded-3xl border border-slate-200 bg-white/90 p-6 shadow-2xl backdrop-blur md:p-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Edit User</h1>
            <p class="text-sm text-slate-600">Update account details, role, department, and activation status.</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center justify-center rounded-full border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100">Back</a>
    </div>

    @if ($errors->any())
        <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <ul class="list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-5" data-confirm="Update this user account?">
        @csrf
        @method('PUT')
        <div>
            <label class="mb-2 block text-sm font-medium text-slate-700">Name</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 @error('name') border-red-500 @enderror" required>
            @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-2 block text-sm font-medium text-slate-700">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 @error('email') border-red-500 @enderror" required>
            @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Role</label>
                <select name="role" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 @error('role') border-red-500 @enderror" required>
                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="it_manager" {{ old('role', $user->role) === 'it_manager' ? 'selected' : '' }}>IT Manager</option>
                    <option value="it_officer" {{ old('role', $user->role) === 'it_officer' ? 'selected' : '' }}>IT Officer</option>
                    <option value="employee" {{ old('role', $user->role) === 'employee' ? 'selected' : '' }}>Employee</option>
                </select>
                @error('role')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Department</label>
                <select name="department_id" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 @error('department_id') border-red-500 @enderror" required>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ old('department_id', $user->department_id) == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                    @endforeach
                </select>
                @error('department_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>
        <div>
            <label class="mb-2 block text-sm font-medium text-slate-700">Status</label>
            <select name="is_active" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 @error('is_active') border-red-500 @enderror" required>
                <option value="1" {{ old('is_active', $user->is_active ? '1' : '0') === '1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ old('is_active', $user->is_active ? '1' : '0') === '0' ? 'selected' : '' }}>Inactive</option>
            </select>
            @error('is_active')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">New Password</label>
                <input type="password" name="password" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 @error('password') border-red-500 @enderror">
                @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Confirm Password</label>
                <input type="password" name="password_confirmation" class="w-full rounded-xl border border-slate-300 px-3 py-2.5">
            </div>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row">
            <button type="submit" class="rounded-full bg-blue-600 px-5 py-2.5 font-medium text-white transition hover:bg-blue-700">Update User</button>
            <a href="{{ route('admin.users.index') }}" class="rounded-full border border-slate-300 px-5 py-2.5 text-center font-medium text-slate-700 transition hover:bg-slate-100">Cancel</a>
        </div>
    </form>
</div>
@endsection
