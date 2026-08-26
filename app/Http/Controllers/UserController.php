<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    private function authorizeUserManagement(Request $request): void
    {
        abort_unless($request->user()?->isSuperAdmin(), 403, 'Only superadmins can manage users.');
    }

    public function index(Request $request)
    {
        $this->authorizeUserManagement(request());
        $query = User::query()->latest();

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->paginate(10)->withQueryString();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $this->authorizeUserManagement(request());
        return view('users.create');
    }

    public function store(Request $request)
    {
        $this->authorizeUserManagement(request());
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', 'in:superadmin,manager,employee'],
            'status' => ['required', 'in:active,inactive'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        User::create($data);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $this->authorizeUserManagement(request());
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeUserManagement(request());
        if ($user->is($request->user()) && $request->status === 'inactive') {
            return back()->withErrors(['status' => 'You cannot deactivate your own account.']);
        }

        if ($user->is($request->user()) && $request->role !== 'superadmin') {
            return back()->withErrors(['role' => 'You cannot remove your own superadmin role.']);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', 'in:superadmin,manager,employee'],
            'status' => ['required', 'in:active,inactive'],
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ]);

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        if ($user->isSuperAdmin() && ($data['role'] ?? $user->role) !== 'superadmin') {
            $remaining = User::where('role', 'superadmin')
                ->where('id', '!=', $user->getKey())
                ->exists();

            if (! $remaining) {
                return back()->withErrors(['role' => 'The last superadmin cannot be demoted. Create another superadmin first.']);
            }
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(Request $request, User $user)
    {
        $this->authorizeUserManagement(request());
        if ($user->is($request->user())) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if ($user->isSuperAdmin()) {
            $remaining = User::where('role', 'superadmin')
                ->where('id', '!=', $user->getKey())
                ->exists();

            if (! $remaining) {
                return back()->with('error', 'The last superadmin cannot be deleted. Create another superadmin first.');
            }
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
