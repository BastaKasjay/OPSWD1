<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('employee')->paginate(10);
        return view('user.index', compact('users'));
    }

    public function create()
    {
        $employees = Employee::all();
        return view('user.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string',
            'employee_id' => 'required|exists:employees,id',
        ]);
        // Only admins can assign any role, others default to 'user'
        if (!$user || $user->role !== 'admin') {
            $validated['role'] = 'user';
        }
        // Password will be hashed by the User model mutator
        User::create($validated);
        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $employees = Employee::all();
        return view('user.edit', compact('user', 'employees'));
    }

    public function update(Request $request, User $user)
    {
        $authUser = auth()->user();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username,' . $user->id,
            'role' => 'required|string',
            'employee_id' => 'required|exists:employees,id',
        ]);
        // Only admins can change role, others keep their current role
        if (!$authUser || $authUser->role !== 'admin') {
            unset($validated['role']);
        }
        if ($request->filled('password')) {
            $validated['password'] = $request->password; // Mutator will hash
        }
        $user->update($validated);
        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $authUser = auth()->user();
        if (!$authUser || $authUser->role !== 'admin') {
            return redirect()->route('users.index')->with('error', 'Only admins can delete users.');
        }
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
