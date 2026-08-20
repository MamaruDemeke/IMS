<?php

namespace App\Http\Controllers;

use App\Http\Requests\MessageStoreRequest;
use App\Http\Requests\TicketStoreRequest;
use App\Http\Requests\TicketUpdateRequest;
use App\Models\AuditLog;
use App\Models\Department;
use App\Models\Ticket;
use App\Models\TicketHistory;
use App\Models\User;
use App\Notifications\TicketActivityNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(Request $request): View
    {
        $tickets = Ticket::query()
            ->with(['user', 'department'])
            ->when(! Gate::check('manage-tickets', $request->user()), function ($query) use ($request) {
                $query->where('user_id', $request->user()?->id);
            })
            ->latest()
            ->get();

        return view('tickets.index', compact('tickets'));
    }

    public function create(Request $request): View
    {
        abort_if(Gate::check('manage-tickets', $request->user()), 403);

        $departments = Department::all();

        return view('tickets.create', compact('departments'));
    }

    public function store(TicketStoreRequest $request): RedirectResponse
    {
        abort_if(Gate::check('manage-tickets', $request->user()), 403);

        $validated = $request->validated();

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('tickets', 'public');
        }

        $ticket = Ticket::query()->create([
            'ticket_number' => 'ITSMS-'.strtoupper(Str::random(6)),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'priority' => $validated['priority'],
            'category' => $validated['category'],
            'status' => 'open',
            'user_id' => $request->user()?->id,
            'department_id' => $validated['department_id'],
            'attachment_path' => $attachmentPath,
        ]);

        TicketHistory::query()->create([
            'ticket_id' => $ticket->id,
            'user_id' => $request->user()?->id,
            'action' => 'created',
            'details' => 'Ticket created by '.($request->user()?->name ?? 'system'),
        ]);

        $this->notifyTicketStaff(
            $ticket,
            'created',
            'A new ticket requires attention: '.$ticket->title,
            $request->user()?->getKey(),
        );

        AuditLog::query()->create([
            'user_id' => $request->user()?->id,
            'action' => 'created',
            'model' => Ticket::class,
            'model_id' => $ticket->id,
            'details' => 'New ticket created: '.$ticket->title,
        ]);

        return redirect()->route('tickets.index')->with('status', 'Ticket created successfully.');
    }

    public function show(Request $request, Ticket $ticket): View
    {
        abort_unless(Gate::check('view-ticket', $ticket), 403);

        $ticket->load(['user', 'department', 'assignedTo', 'histories.user']);

        return view('tickets.show', compact('ticket'));
    }

    public function message(MessageStoreRequest $request, Ticket $ticket): RedirectResponse
    {
        abort_unless(Gate::check('respond-to-ticket', $ticket), 403);

        $validated = $request->validated();

        $isItStaff = Gate::check('manage-tickets', $request->user());

        TicketHistory::query()->create([
            'ticket_id' => $ticket->getKey(),
            'user_id' => $request->user()?->getKey(),
            'action' => $isItStaff ? 'responded' : 'replied',
            'details' => $validated['message'],
        ]);

        if ($isItStaff) {
            $this->notifyTicketOwner($ticket, 'responded', 'IT support replied: '.$validated['message']);
        } else {
            $this->notifyTicketStaff($ticket, 'replied', 'The requester replied: '.$validated['message'], $request->user()?->getKey());
        }

        AuditLog::query()->create([
            'user_id' => $request->user()?->getKey(),
            'action' => 'message',
            'model' => Ticket::class,
            'model_id' => $ticket->getKey(),
            'details' => 'Ticket conversation updated by '.($request->user()?->name ?? 'system').': '.$validated['message'],
        ]);

        return redirect()->route('tickets.show', $ticket)->with('status', 'Message sent successfully.');
    }

    public function update(TicketUpdateRequest $request, Ticket $ticket): RedirectResponse
    {
        abort_unless(Gate::check('manage-tickets', $request->user()), 403);

        $validated = $request->validated();

        $previousStatus = $ticket->getAttribute('status');
        $previousPriority = $ticket->getAttribute('priority');
        $previousAssignedTo = $ticket->getAttribute('assigned_to');
        $notificationDetails = [];

        $ticket->update([
            'status' => $validated['status'],
            'priority' => $validated['priority'] ?? $previousPriority,
            'assigned_to' => $validated['assigned_to'] ?? $previousAssignedTo,
        ]);
        $ticket->refresh()->load('assignedTo');

        if (! empty($validated['response'])) {
            TicketHistory::query()->create([
                'ticket_id' => $ticket->getKey(),
                'user_id' => $request->user()?->getKey(),
                'action' => 'responded',
                'details' => $validated['response'],
            ]);
            $notificationDetails[] = 'IT support replied: '.$validated['response'];
        }

        if ($previousStatus !== $validated['status']) {
            TicketHistory::query()->create([
                'ticket_id' => $ticket->getKey(),
                'user_id' => $request->user()?->getKey(),
                'action' => 'status_changed',
                'details' => 'Status updated from '.ucfirst($previousStatus).' to '.ucfirst($validated['status']),
            ]);
            $notificationDetails[] = 'Status changed to '.ucfirst($validated['status']);
        }

        if (($validated['priority'] ?? null) && $previousPriority !== $validated['priority']) {
            TicketHistory::query()->create([
                'ticket_id' => $ticket->getKey(),
                'user_id' => $request->user()?->getKey(),
                'action' => 'priority_changed',
                'details' => 'Priority updated from '.ucfirst($previousPriority).' to '.ucfirst($validated['priority']),
            ]);
            $notificationDetails[] = 'Priority changed to '.ucfirst($validated['priority']);
        }

        if (($validated['assigned_to'] ?? null) && $previousAssignedTo !== $validated['assigned_to']) {
            TicketHistory::query()->create([
                'ticket_id' => $ticket->getKey(),
                'user_id' => $request->user()?->getKey(),
                'action' => 'assigned',
                'details' => 'Ticket assigned to '.($ticket->assignedTo?->name ?? 'Unknown user'),
            ]);

            if ($ticket->assignedTo !== null) {
                $ticket->assignedTo->notify(new TicketActivityNotification($ticket, 'assigned', 'You have been assigned to this ticket.'));
            }
        }

        if ($notificationDetails !== []) {
            $this->notifyTicketOwner($ticket, 'updated', implode('. ', $notificationDetails));
        }

        AuditLog::query()->create([
            'user_id' => $request->user()?->getKey(),
            'action' => 'updated',
            'model' => Ticket::class,
            'model_id' => $ticket->getKey(),
            'details' => 'Ticket updated by officer: '.$ticket->getAttribute('title').' | status: '.($validated['status'] ?? $ticket->getAttribute('status')).' | priority: '.($validated['priority'] ?? $ticket->getAttribute('priority')),
        ]);

        return redirect()->route('tickets.show', $ticket)->with('status', 'Ticket updated successfully.');
    }

    public function download(Request $request, Ticket $ticket)
    {
        abort_unless(Gate::check('view-ticket', $ticket), 403);
        abort_unless($ticket->attachment_path, 404);

        return Storage::disk('public')->download($ticket->attachment_path, $ticket->ticket_number.'_attachment');
    }

    private function notifyTicketOwner(Ticket $ticket, string $action, string $details): void
    {
        $ticket->user?->notify(new TicketActivityNotification($ticket, $action, $details));
    }

    private function notifyTicketStaff(Ticket $ticket, string $action, string $details, ?int $excludeUserId = null): void
    {
        $staff = User::query()
            ->whereIn('role', ['admin', 'it_manager', 'it_officer'])
            ->where('is_active', true)
            ->when($excludeUserId !== null, function ($query) use ($excludeUserId) {
                $query->whereKeyNot($excludeUserId);
            })
            ->get();

        Notification::send($staff, new TicketActivityNotification($ticket, $action, $details));
    }
}
