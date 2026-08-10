<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\TicketHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TicketHistoryController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless(Gate::check('view-ticket-history'), 403);

        $histories = TicketHistory::query()
            ->with(['ticket.user', 'ticket.department', 'user'])
            ->when($request->filled('date'), function ($query) use ($request) {
                $query->whereDate('created_at', $request->input('date'));
            })
            ->when($request->filled('from_date') && $request->filled('to_date'), function ($query) use ($request) {
                $query->whereBetween('created_at', [$request->input('from_date'), $request->input('to_date')]);
            })
            ->when($request->filled('employee'), function ($query) use ($request) {
                $query->whereHas('ticket.user', function ($q) use ($request) {
                    $q->where('name', 'like', "%{$request->input('employee')}%");
                });
            })
            ->when($request->filled('department_id'), function ($query) use ($request) {
                $query->whereHas('ticket.department', function ($q) use ($request) {
                    $q->where('id', $request->input('department_id'));
                });
            })
            ->when($request->filled('priority'), function ($query) use ($request) {
                $query->whereHas('ticket', function ($q) use ($request) {
                    $q->where('priority', $request->input('priority'));
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->whereHas('ticket', function ($q) use ($request) {
                    $q->where('status', $request->input('status'));
                });
            })
            ->when($request->filled('it_officer'), function ($query) use ($request) {
                $query->whereHas('user', function ($q) use ($request) {
                    $q->where('name', 'like', "%{$request->input('it_officer')}%");
                });
            })
            ->latest()
            ->get();

        $departments = Department::query()->orderBy('name')->get();
        $officers = User::query()->whereIn('role', ['it_manager', 'it_officer'])->orderBy('name')->get();
        $employees = User::query()->where('role', 'employee')->orderBy('name')->get();

        return view('ticket-histories.index', compact('histories', 'departments', 'officers', 'employees'));
    }
}
