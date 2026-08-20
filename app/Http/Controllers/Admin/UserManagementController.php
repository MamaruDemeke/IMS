<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()?->role === 'admin', 403);

        $users = User::query()
            ->with('department')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('role'), function ($query) use ($request) {
                $query->where('role', $request->input('role'));
            })
            ->when($request->filled('department_id'), function ($query) use ($request) {
                $query->where('department_id', $request->input('department_id'));
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $status = $request->input('status');
                $query->where('is_active', $status === 'active');
            })
            ->latest()
            ->get();

        $departments = Department::query()->orderBy('name')->get();

        return view('admin.users.index', compact('users', 'departments'));
    }

    public function create(Request $request): View
    {
        abort_unless($request->user()?->role === 'admin', 403);

        $departments = Department::query()->orderBy('name')->get();

        return view('admin.users.create', compact('departments'));
    }

    public function store(UserStoreRequest $request): RedirectResponse
    {
        abort_unless($request->user()?->role === 'admin', 403);

        $validated = $request->validated();

        User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'department_id' => $validated['department_id'],
            'is_active' => $validated['is_active'],
        ]);

        return redirect()->route('admin.users.index')->with('status', 'User created successfully.');
    }

    public function edit(Request $request, User $user): View
    {
        abort_unless($request->user()?->role === 'admin', 403);

        $departments = Department::query()->orderBy('name')->get();

        return view('admin.users.edit', compact('user', 'departments'));
    }

    public function update(UserUpdateRequest $request, User $user): RedirectResponse
    {
        abort_unless($request->user()?->role === 'admin', 403);

        $validated = $request->validated();
        $wasInactive = ! $user->is_active;

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'department_id' => $validated['department_id'],
            'is_active' => $validated['is_active'],
            ...(filled($validated['password']) ? ['password' => Hash::make($validated['password'])] : []),
        ]);

        if ($wasInactive && $user->is_active) {
            $user->resetLoginAttempts();
        }

        return redirect()->route('admin.users.index')->with('status', $wasInactive && $user->is_active
            ? 'User reactivated and login attempts reset successfully.'
            : 'User updated successfully.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        abort_unless($request->user()?->role === 'admin', 403);

        $user->delete();

        return redirect()->route('admin.users.index')->with('status', 'User deleted successfully.');
    }

    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        abort_unless($request->user()?->role === 'admin', 403);

        $validated = $request->validate([
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.users.index')->with('status', 'Password reset successfully.');
    }
}
