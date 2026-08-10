<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminSettingsController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()?->role === 'admin', 403);

        $settings = AdminSetting::query()->get()->pluck('value', 'key');

        return view('admin.settings.index', compact('settings'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->role === 'admin', 403);

        $request->validate([
            'ticket_notifications_enabled' => ['nullable', 'boolean'],
            'user_management_enabled' => ['nullable', 'boolean'],
            'ticket_creation_enabled' => ['nullable', 'boolean'],
        ]);

        $toggles = [
            'ticket_notifications_enabled' => $request->boolean('ticket_notifications_enabled'),
            'user_management_enabled' => $request->boolean('user_management_enabled'),
            'ticket_creation_enabled' => $request->boolean('ticket_creation_enabled'),
        ];

        foreach ($toggles as $key => $value) {
            AdminSetting::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $value ? '1' : '0', 'description' => ucfirst(str_replace('_', ' ', $key))]
            );
        }

        return redirect()->route('admin.settings.index')->with('status', 'Settings updated successfully.');
    }
}
