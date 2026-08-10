<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Settings</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50">
<div class="container mx-auto p-6 max-w-3xl">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">Admin Settings</h1>
            <p class="text-slate-600">Manage service control switches and system behavior.</p>
        </div>
        <a href="/admin/users" class="bg-slate-800 text-white px-4 py-2 rounded">User Management</a>
    </div>

    @if (session('status'))
        <div class="bg-green-100 text-green-800 px-4 py-3 rounded mb-4">{{ session('status') }}</div>
    @endif

    <form action="/admin/settings" method="POST" class="bg-white rounded-lg shadow p-6 space-y-5">
        @csrf

        <div class="flex items-center justify-between">
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

        <div class="flex items-center justify-between">
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

        <div class="flex items-center justify-between">
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

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Save Settings</button>
    </form>
</div>

<script>
    document.querySelectorAll('[data-open-reset]').forEach(function (button) {
        button.addEventListener('click', function () {
            const target = document.getElementById(button.getAttribute('data-open-reset'));
            if (target) {
                target.classList.remove('hidden');
            }
        });
    });

    document.querySelectorAll('[data-close-reset]').forEach(function (button) {
        button.addEventListener('click', function () {
            const target = document.getElementById(button.getAttribute('data-close-reset'));
            if (target) {
                target.classList.add('hidden');
            }
        });
    });
</script>
</body>
</html>
