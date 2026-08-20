<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Ticket;
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
        $unreadNotifications = $request->user()?->unreadNotifications()->count() ?? 0;
        $recentNotifications = $request->user()?->notifications()
            ->where('created_at', '>=', now()->subDays(3))
            ->latest()
            ->take(20)
            ->get() ?? collect();

        return view('dashboard', compact('tickets', 'openTickets', 'users', 'departments', 'unreadNotifications', 'recentNotifications', 'canManageTickets'));
    }
}
