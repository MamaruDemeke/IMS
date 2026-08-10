<?php

namespace App\Providers;

use App\Models\Ticket;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot(): void
    {
        Gate::define('admin-access', function ($user) {
            return $user?->role === 'admin';
        });

        Gate::define('manage-tickets', function ($user) {
            return in_array($user?->role, ['it_manager', 'it_officer'], true);
        });

        Gate::define('respond-to-ticket', function ($user, ?Ticket $ticket = null) {
            return $ticket !== null && Gate::forUser($user)->check('manage-tickets');
        });

        Gate::define('view-ticket', function ($user, ?Ticket $ticket = null) {
            if (Gate::forUser($user)->check('manage-tickets')) {
                return true;
            }

            return $ticket !== null && $ticket->getAttribute('user_id') === $user?->getKey();
        });

        Gate::define('view-ticket-history', function ($user) {
            return Gate::forUser($user)->check('manage-tickets');
        });
    }
}
