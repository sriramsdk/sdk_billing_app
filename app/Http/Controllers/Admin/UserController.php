<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::with('employee')
            ->when($request->search, fn ($query, $search) => $query->where(function ($query) use ($search): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            }))
            ->when($request->role, fn ($query, $role) => $query->where('role', $role))
            ->when($request->status, fn ($query, $status) => $query->where('is_active', $status === 'active'))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(UserRequest $request): RedirectResponse
    {
        User::create([
            ...$request->validated(),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User was created.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    public function status(User $user): RedirectResponse
    {
        if ($user->is(auth()->user())) {
            return back()->with('error', 'You cannot deactivate your current account.');
        }

        $isActive = ! $user->is_active;
        $user->update(['is_active' => $isActive]);
        $user->employee?->update(['is_active' => $isActive]);

        return back()->with('success', 'User status was updated.');
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        if ($user->is(auth()->user()) && (! $request->boolean('is_active') || $request->input('role') !== 'admin')) {
            return back()->with('error', 'You cannot deactivate or remove the administrator role from your current account.');
        }

        $data = $request->validated();
        if (empty($data['password'])) {
            unset($data['password']);
        }
        $user->update([
            ...$data,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User was updated.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->is(auth()->user())) {
            return back()->with('error', 'You cannot delete your current account.');
        }
        if ($user->employee || $user->auditLogs()->exists()) {
            return back()->with('error', 'Users connected to employee or audit records cannot be deleted.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User was deleted.');
    }
}