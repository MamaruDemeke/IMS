<?php

namespace App\Http\Controllers;

use App\Models\TicketHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user() !== null, 403);

        $notifications = TicketHistory::query()
            ->with(['ticket.user', 'ticket.department', 'user'])
            ->where('is_read', false)
            ->whereHas('ticket', function ($ticketQuery) use ($request) {
                $ticketQuery->where('user_id', $request->user()?->getKey());
            })
            ->latest()
            ->get();

        return view('notifications.index', compact('notifications'));
    }

    public function open(Request $request, TicketHistory $history): RedirectResponse
    {
        abort_unless($history->ticket?->user_id === $request->user()?->getKey() || Gate::check('manage-tickets', $request->user()), 403);

        $history->update(['is_read' => true]);

        return $history->ticket !== null
            ? redirect()->route('tickets.show', $history->ticket)
            : redirect()->route('notifications.index');
    }
}
