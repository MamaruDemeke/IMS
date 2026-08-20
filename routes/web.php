<?php

use App\Http\Controllers\Admin\AdminSettingsController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItCommunicationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketHistoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::middleware(['web', 'active.user'])->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo');
        Route::delete('/profile/photo', [ProfileController::class, 'removePhoto'])->name('profile.photo.remove');
        Route::get('/it-communications', [ItCommunicationController::class, 'index'])->name('it-communications.index');
        Route::post('/it-communications', [ItCommunicationController::class, 'store'])->name('it-communications.store');
        Route::delete('/it-communications/{message}', [ItCommunicationController::class, 'destroy'])->name('it-communications.destroy');
        Route::get('/it-communications/updates', [ItCommunicationController::class, 'updates'])->name('it-communications.updates');
        Route::get('/it-communications/unread-count', [ItCommunicationController::class, 'unreadCount'])->name('it-communications.unread-count');
        Route::get('/ticket-histories', [TicketHistoryController::class, 'index'])->name('ticket-histories.index');
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/notifications/{notification}/open', [NotificationController::class, 'open'])->name('notifications.open');
        Route::resource('tickets', TicketController::class);
        Route::get('/tickets/{ticket}/download', [TicketController::class, 'download'])->name('tickets.download');
        Route::post('/tickets/{ticket}/messages', [TicketController::class, 'message'])->name('tickets.messages.store');
        Route::post('/tickets/{ticket}/confirm-resolution', [TicketController::class, 'confirmResolution'])->name('tickets.confirm-resolution');
        Route::post('/tickets/{ticket}/request-assistance', [TicketController::class, 'requestFurtherAssistance'])->name('tickets.request-assistance');

        Route::middleware('can:admin-access')->group(function () {
            Route::get('/admin/users', [UserManagementController::class, 'index'])->name('admin.users.index');
            Route::get('/admin/users/create', [UserManagementController::class, 'create'])->name('admin.users.create');
            Route::post('/admin/users', [UserManagementController::class, 'store'])->name('admin.users.store');
            Route::get('/admin/users/{user}/edit', [UserManagementController::class, 'edit'])->name('admin.users.edit');
            Route::put('/admin/users/{user}', [UserManagementController::class, 'update'])->name('admin.users.update');
            Route::delete('/admin/users/{user}', [UserManagementController::class, 'destroy'])->name('admin.users.destroy');
            Route::post('/admin/users/{user}/reset-password', [UserManagementController::class, 'resetPassword'])->name('admin.users.reset-password');

            Route::get('/admin/settings', [AdminSettingsController::class, 'index'])->name('admin.settings.index');
            Route::post('/admin/settings', [AdminSettingsController::class, 'store'])->name('admin.settings.store');
        });
    });
});
