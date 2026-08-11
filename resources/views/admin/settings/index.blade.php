@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <div class="flex flex-col gap-4 rounded-3xl border border-slate-200 bg-white/90 p-6 shadow-xl backdrop-blur md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold">Admin Settings</h1>
            <p class="text-slate-600">Manage service control switches and system behavior.</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="rounded-full bg-slate-800 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-700">User Management</a>
    </div>

    @if (session('status'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('status') }}</div>
    @endif

    <form action="{{ route('admin.settings.store') }}" method="POST" class="space-y-5 rounded-3xl border border-slate-200 bg-white p-6 shadow-xl" data-confirm="Save these admin settings?">
        @csrf

        <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 p-4">
            <div>
                <h2 class="font-semibold">Ticket Notifications</h2>
                <p class="text-sm text-slate-600">Enable or disable dashboard notifications on the IT support area.</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-sm font-medium {{ ($settings['ticket_notifications_enabled'] ?? '0') == '1' ? 'text-green-600' : 'text-red-600' }}">
                    {{ ($settings['ticket_notifications_enabled'] ?? '0') == '1' ? 'On' : 'Off' }}
                </span>
                <input type="checkbox" name="ticket_notifications_enabled" value="1" {{ ($settings['ticket_notifications_enabled'] ?? '0') == '1' ? 'checked' : '' }}>
            </div>
        </div>

        <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 p-4">
            <div>
                <h2 class="font-semibold">User Management</h2>
                <p class="text-sm text-slate-600">Allow admin user CRUD and password reset controls.</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-sm font-medium {{ ($settings['user_management_enabled'] ?? '0') == '1' ? 'text-green-600' : 'text-red-600' }}">
                    {{ ($settings['user_management_enabled'] ?? '0') == '1' ? 'On' : 'Off' }}
                </span>
                <input type="checkbox" name="user_management_enabled" value="1" {{ ($settings['user_management_enabled'] ?? '0') == '1' ? 'checked' : '' }}>
            </div>
        </div>

        <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 p-4">
            <div>
                <h2 class="font-semibold">Ticket Creation</h2>
                <p class="text-sm text-slate-600">Enable or disable ticket creation for staff users.</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-sm font-medium {{ ($settings['ticket_creation_enabled'] ?? '0') == '1' ? 'text-green-600' : 'text-red-600' }}">
                    {{ ($settings['ticket_creation_enabled'] ?? '0') == '1' ? 'On' : 'Off' }}
                </span>
                <input type="checkbox" name="ticket_creation_enabled" value="1" {{ ($settings['ticket_creation_enabled'] ?? '0') == '1' ? 'checked' : '' }}>
            </div>
        </div>

        <button type="submit" class="rounded-full bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700">Save Settings</button>
    </form>
</div>
@endsection
