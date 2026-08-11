<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Ticket;
use App\Models\TicketHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $canManageTickets = Gate::check('manage-tickets', $request->user());
        $ticketQuery = Ticket::query()
            ->when(! $canManageTickets, function ($query) use ($request) {
                $query->where('user_id', $request->user()?->getKey());
            });

        $tickets = (clone $ticketQuery)->count();
        $openTickets = (clone $ticketQuery)->where('status', 'open')->count();
        $users = $canManageTickets ? User::query()->count() : null;
        $departments = $canManageTickets ? Department::query()->count() : null;
        $historyQuery = TicketHistory::query()
            ->when(! $canManageTickets, function ($query) use ($request) {
                $query->whereHas('ticket', function ($ticketQuery) use ($request) {
                    $ticketQuery->where('user_id', $request->user()?->getKey());
                });
            });
        $communicationNotifications = (clone $historyQuery)->count();
        $unreadNotifications = (clone $historyQuery)
            ->where('is_read', false)
            ->count();
        $recentNotifications = TicketHistory::query()
            ->with(['ticket.user', 'ticket.department', 'user'])
            ->when(! $canManageTickets, function ($query) use ($request) {
                $query->whereHas('ticket', function ($ticketQuery) use ($request) {
                    $ticketQuery->where('user_id', $request->user()?->getKey());
                });
            })
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact('tickets', 'openTickets', 'users', 'departments', 'communicationNotifications', 'unreadNotifications', 'recentNotifications', 'canManageTickets'));
    }
}
