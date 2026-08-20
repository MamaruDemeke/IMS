<?php

use App\Models\Department;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\TicketActivityNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can access the dashboard', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'department_id' => 1,
    ]);

    $this->actingAs($admin)
        ->get('/dashboard')
        ->assertOk();
});

test('it staff dashboard shows recent ticket activity', function () {
    $department = Department::create([
        'name' => 'IT',
        'code' => 'IT',
        'description' => 'Information Technology',
    ]);

    $itOfficer = User::factory()->create([
        'role' => 'it_officer',
        'department_id' => $department->id,
    ]);

    $staff = User::factory()->create([
        'name' => 'Staff User',
        'role' => 'employee',
        'department_id' => $department->id,
    ]);

    $ticket = Ticket::query()->create([
        'ticket_number' => 'ITSMS-TEST01',
        'title' => 'VPN access issue',
        'description' => 'Cannot connect to VPN from remote office.',
        'priority' => 'high',
        'category' => 'network',
        'status' => 'open',
        'user_id' => $staff->id,
        'department_id' => $department->id,
    ]);

    $ticket->histories()->create([
        'user_id' => $staff->id,
        'action' => 'created',
        'details' => 'Cannot connect to VPN from remote office.',
    ]);
    $itOfficer->notify(new TicketActivityNotification($ticket, 'created', 'Cannot connect to VPN from remote office.'));

    $this->actingAs($itOfficer)
        ->get('/dashboard')
        ->assertOk()
        ->assertSee('Recent Notifications')
        ->assertSee('Cannot connect to VPN from remote office.')
        ->assertSee('VPN access issue');
});

test('employee can create a support ticket', function () {
    $department = Department::create([
        'name' => 'IT',
        'code' => 'IT',
        'description' => 'Information Technology',
    ]);

    $employee = User::factory()->create([
        'role' => 'employee',
        'department_id' => $department->id,
    ]);
    $itOfficer = User::factory()->create([
        'role' => 'it_officer',
        'department_id' => $department->id,
    ]);

    $response = $this->actingAs($employee)
        ->post('/tickets', [
            'title' => 'VPN access issue',
            'description' => 'Cannot connect to VPN from remote office.',
            'priority' => 'high',
            'category' => 'network',
            'department_id' => $department->id,
        ]);

    $response->assertRedirect('/tickets');
    expect($itOfficer->unreadNotifications()->count())->toBe(1);
});

test('employees only see their own ticket activity on the dashboard', function () {
    $department = Department::create([
        'name' => 'Operations',
        'code' => 'OPS',
        'description' => 'Operations department',
    ]);

    $employee = User::factory()->create(['role' => 'employee', 'department_id' => $department->id]);
    $otherEmployee = User::factory()->create(['role' => 'employee', 'department_id' => $department->id]);
    $ownTicket = Ticket::create([
        'ticket_number' => 'ITSMS-DASH1',
        'title' => 'My ticket',
        'description' => 'My private ticket',
        'status' => 'open',
        'priority' => 'medium',
        'category' => 'Hardware',
        'user_id' => $employee->id,
        'department_id' => $department->id,
    ]);
    $otherTicket = Ticket::create([
        'ticket_number' => 'ITSMS-DASH2',
        'title' => 'Other ticket',
        'description' => 'Another private ticket',
        'status' => 'open',
        'priority' => 'medium',
        'category' => 'Hardware',
        'user_id' => $otherEmployee->id,
        'department_id' => $department->id,
    ]);

    $ownTicket->histories()->create(['user_id' => $employee->id, 'action' => 'created', 'details' => 'My private ticket activity']);
    $otherTicket->histories()->create(['user_id' => $otherEmployee->id, 'action' => 'created', 'details' => 'Another employee ticket activity']);
    $employee->notify(new TicketActivityNotification($ownTicket, 'created', 'My private ticket activity'));
    $otherEmployee->notify(new TicketActivityNotification($otherTicket, 'created', 'Another employee ticket activity'));

    $this->actingAs($employee)
        ->get('/dashboard')
        ->assertOk()
        ->assertSee('My private ticket activity')
        ->assertDontSee('Another employee ticket activity')
        ->assertSee('Recent Notifications');
});

test('employees can view ticket history and notification buttons on the dashboard', function () {
    $department = Department::create([
        'name' => 'Support',
        'code' => 'SUP',
        'description' => 'Support department',
    ]);

    $employee = User::factory()->create(['role' => 'employee', 'department_id' => $department->id]);
    $ticket = Ticket::create([
        'ticket_number' => 'ITSMS-NOTIF1',
        'title' => 'Printer issue',
        'description' => 'Printer is offline',
        'status' => 'open',
        'priority' => 'medium',
        'category' => 'Hardware',
        'user_id' => $employee->id,
        'department_id' => $department->id,
    ]);

    $ticket->histories()->create([
        'user_id' => $employee->id,
        'action' => 'created',
        'details' => 'Ticket created by the employee',
        'is_read' => false,
    ]);
    $employee->notify(new TicketActivityNotification($ticket, 'created', 'Ticket created by the employee'));

    $this->actingAs($employee)
        ->get('/dashboard')
        ->assertOk()
        ->assertSee('Ticket History')
        ->assertSee('Notifications')
        ->assertSee('1');
});

test('employees can open unread notifications from their dashboard', function () {
    $department = Department::create([
        'name' => 'Support',
        'code' => 'SUP',
        'description' => 'Support department',
    ]);

    $employee = User::factory()->create(['role' => 'employee', 'department_id' => $department->id]);
    $ticket = Ticket::create([
        'ticket_number' => 'ITSMS-NOTIF2',
        'title' => 'VPN issue',
        'description' => 'Cannot connect to VPN',
        'status' => 'open',
        'priority' => 'high',
        'category' => 'Network',
        'user_id' => $employee->id,
        'department_id' => $department->id,
    ]);

    $employee->notify(new TicketActivityNotification($ticket, 'responded', 'New update from IT'));
    $notification = $employee->notifications()->firstOrFail();

    $this->actingAs($employee)
        ->get('/notifications')
        ->assertOk()
        ->assertSee('New update from IT')
        ->assertSee('VPN issue');

    $this->actingAs($employee)
        ->get('/notifications/'.$notification->id.'/open')
        ->assertRedirect();
});

test('dashboard only shows a user notifications from the last three days in a scrollable list', function () {
    $department = Department::create([
        'name' => 'Support',
        'code' => 'SUP',
        'description' => 'Support department',
    ]);
    $employee = User::factory()->create(['role' => 'employee', 'department_id' => $department->id]);
    $ticket = Ticket::create([
        'ticket_number' => 'ITSMS-THREEDAY',
        'title' => 'Laptop support',
        'description' => 'Laptop issue',
        'status' => 'open',
        'priority' => 'medium',
        'category' => 'Hardware',
        'user_id' => $employee->id,
        'department_id' => $department->id,
    ]);

    $employee->notify(new TicketActivityNotification($ticket, 'responded', 'Recent support response'));
    $employee->notify(new TicketActivityNotification($ticket, 'responded', 'Old support response'));
    $employee->notifications()
        ->get()
        ->first(fn ($notification) => $notification->data['details'] === 'Old support response')
        ?->update(['created_at' => now()->subDays(4)]);

    $this->actingAs($employee)
        ->get('/dashboard')
        ->assertOk()
        ->assertSee('Recent support response')
        ->assertDontSee('Old support response')
        ->assertSee('overflow-y-auto', false);
});
