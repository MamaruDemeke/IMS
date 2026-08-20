<?php

use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a final incorrect login attempt deactivates the account after the one-minute countdown', function () {
    $department = Department::create([
        'name' => 'Support',
        'code' => 'SUP',
        'description' => 'Support department',
    ]);
    $user = User::factory()->create([
        'email' => 'employee@example.com',
        'department_id' => $department->id,
    ]);

    foreach (range(1, 3) as $attempt) {
        $this->from('/login')
            ->post('/login', ['email' => $user->email, 'password' => 'incorrect-password'])
            ->assertRedirect('/login');
    }

    expect($user->refresh()->failed_login_attempts)->toBe(3)
        ->and($user->final_login_attempt_available_at)->not->toBeNull()
        ->and($user->is_active)->toBeTrue();

    $this->travel(1)->minute();

    $this->from('/login')
        ->post('/login', ['email' => $user->email, 'password' => 'incorrect-password'])
        ->assertRedirect('/login');

    expect($user->refresh()->is_active)->toBeFalse();
});
