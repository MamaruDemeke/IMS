<?php

use App\Models\Department;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('employees can only view their own tickets while it staff can view any ticket', function () {
    $department = Department::create([
        'name' => 'IT Support',
        'code' => 'IT',
        'description' => 'IT support department',
    ]);

    $employee = User::factory()->create([
        'name' => 'Employee One',
        'email' => 'employee1@example.com',
        'role' => 'employee',
        'department_id' => $department->id,
    ]);

    $otherEmployee = User::factory()->create([
        'name' => 'Employee Two',
        'email' => 'employee2@example.com',
        'role' => 'employee',
        'department_id' => $department->id,
    ]);

    $itOfficer = User::factory()->create([
        'name' => 'IT Officer',
        'email' => 'it-officer@example.com',
        'role' => 'it_officer',
        'department_id' => $department->id,
    ]);

    $ownTicket = Ticket::create([
        'ticket_number' => 'ITSMS-OWN1',
        'title' => 'Own ticket',
        'description' => 'Employee own ticket',
        'status' => 'open',
        'priority' => 'medium',
        'category' => 'Hardware',
        'user_id' => $employee->id,
        'department_id' => $department->id,
    ]);

    $otherTicket = Ticket::create([
        'ticket_number' => 'ITSMS-OTH1',
        'title' => 'Other ticket',
        'description' => 'Employee other ticket',
        'status' => 'open',
        'priority' => 'high',
        'category' => 'Software',
        'user_id' => $otherEmployee->id,
        'department_id' => $department->id,
    ]);

    $this->actingAs($employee)->get(route('tickets.show', $ownTicket))->assertOk();
    $this->actingAs($employee)->get(route('tickets.show', $otherTicket))->assertForbidden();

    $this->actingAs($itOfficer)->get(route('tickets.show', $otherTicket))->assertOk();
});

test('employees can reply to their own tickets but not other employees tickets', function () {
    $department = Department::create([
        'name' => 'HR',
        'code' => 'HR',
        'description' => 'HR department',
    ]);

    $employee = User::factory()->create([
        'name' => 'Employee Three',
        'email' => 'employee3@example.com',
        'role' => 'employee',
        'department_id' => $department->id,
    ]);

    $otherEmployee = User::factory()->create([
        'name' => 'Employee Four',
        'email' => 'employee4@example.com',
        'role' => 'employee',
        'department_id' => $department->id,
    ]);

    $ownTicket = Ticket::create([
        'ticket_number' => 'ITSMS-OWN2',
        'title' => 'Employee reply test',
        'description' => 'An employee ticket',
        'status' => 'open',
        'priority' => 'low',
        'category' => 'Network',
        'user_id' => $employee->id,
        'department_id' => $department->id,
    ]);

    $otherTicket = Ticket::create([
        'ticket_number' => 'ITSMS-OTH2',
        'title' => 'Other ticket for reply test',
        'description' => 'Another employee ticket',
        'status' => 'open',
        'priority' => 'low',
        'category' => 'Network',
        'user_id' => $otherEmployee->id,
        'department_id' => $department->id,
    ]);

    $this->actingAs($employee)
        ->post(route('tickets.messages.store', $ownTicket), ['message' => 'I have more details for IT.'])
        ->assertRedirect(route('tickets.show', $ownTicket));

    $this->actingAs($employee)
        ->post(route('tickets.messages.store', $otherTicket), ['message' => 'This should be blocked'])
        ->assertForbidden();

    expect($ownTicket->histories()->where('action', 'replied')->count())->toBe(1);
});

test('it managers and it officers can respond to tickets', function () {
    $department = Department::create([
        'name' => 'Finance',
        'code' => 'FIN',
        'description' => 'Finance department',
    ]);

    $employee = User::factory()->create([
        'role' => 'employee',
        'department_id' => $department->id,
    ]);

    $ticket = Ticket::create([
        'ticket_number' => 'ITSMS-REPLY',
        'title' => 'Printer issue',
        'description' => 'Printer is offline',
        'status' => 'open',
        'priority' => 'medium',
        'category' => 'Hardware',
        'user_id' => $employee->id,
        'department_id' => $department->id,
    ]);

    foreach (['it_manager', 'it_officer'] as $role) {
        $itStaff = User::factory()->create([
            'role' => $role,
            'department_id' => $department->id,
        ]);

        $this->actingAs($itStaff)
            ->post(route('tickets.messages.store', $ticket), ['message' => 'We are investigating this issue.'])
            ->assertRedirect(route('tickets.show', $ticket));
    }

    expect($ticket->histories()->where('action', 'responded')->count())->toBe(2);
});

test('employees can only view their own ticket history', function () {
    $department = Department::create([
        'name' => 'Operations',
        'code' => 'OPS',
        'description' => 'Operations department',
    ]);

    $employee = User::factory()->create([
        'name' => 'Employee Five',
        'email' => 'employee5@example.com',
        'role' => 'employee',
        'department_id' => $department->id,
    ]);

    $otherEmployee = User::factory()->create([
        'name' => 'Employee Six',
        'email' => 'employee6@example.com',
        'role' => 'employee',
        'department_id' => $department->id,
    ]);

    $ownTicket = Ticket::create([
        'ticket_number' => 'ITSMS-HIST1',
        'title' => 'Own history',
        'description' => 'Own ticket history',
        'status' => 'open',
        'priority' => 'medium',
        'category' => 'Hardware',
        'user_id' => $employee->id,
        'department_id' => $department->id,
    ]);
    $otherTicket = Ticket::create([
        'ticket_number' => 'ITSMS-HIST2',
        'title' => 'Other history',
        'description' => 'Other ticket history',
        'status' => 'open',
        'priority' => 'medium',
        'category' => 'Hardware',
        'user_id' => $otherEmployee->id,
        'department_id' => $department->id,
    ]);
    $ownTicket->histories()->create(['user_id' => $employee->id, 'action' => 'created', 'details' => 'My ticket communication']);
    $otherTicket->histories()->create(['user_id' => $otherEmployee->id, 'action' => 'created', 'details' => 'Other ticket communication']);

    $this->actingAs($employee)
        ->get(route('ticket-histories.index'))
        ->assertOk()
        ->assertSee('My ticket communication')
        ->assertDontSee('Other ticket communication');
});
