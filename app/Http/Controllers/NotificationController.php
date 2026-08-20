<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user() !== null, 403);

        $notifications = $request->user()?->unreadNotifications()
            ->latest()
            ->get() ?? collect();

        return view('notifications.index', compact('notifications'));
    }

    public function open(Request $request, DatabaseNotification $notification): RedirectResponse
    {
        abort_unless($notification->notifiable_id === $request->user()?->getKey(), 403);

        $notification->markAsRead();

        $ticketId = $notification->data['ticket_id'] ?? null;

        return $ticketId !== null
            ? redirect()->route('tickets.show', $ticketId)
            : redirect()->route('notifications.index');
    }
}
