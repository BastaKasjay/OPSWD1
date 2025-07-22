<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('employee', 'roles')->paginate(10);
        $employees = Employee::all();
        return view('user.index', compact('users', 'employees'));
    }

    public function create()
    {
        $employees = Employee::whereDoesntHave('user')->get(); 
        $roles = Role::all();
        return view('user.create', compact('employees', 'roles'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        $employee = Employee::findOrFail($validated['employee_id']);

        $fullName = $employee->first_name;
        if (!empty($employee->middle_name)) {
            $fullName .= ' ' . strtoupper(substr($employee->middle_name, 0, 1)) . '.';
        }
        $fullName .= ' ' . $employee->last_name;

        $userData = [
            'employee_id' => $validated['employee_id'],
            'username' => $validated['username'],
            'name' => $fullName,
            'password' => Hash::make($validated['password']),
        ];

        $newUser = User::create($userData);

        // Admin can assign a role, others will default to basic user role
        if ($user && $user->roles()->where('name', 'admin')->exists()) {
            $newUser->roles()->attach($validated['role_id']);
        } else {
            $defaultRole = Role::where('name', 'user')->first();
            $newUser->roles()->attach($defaultRole->id);
        }

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $employees = Employee::all();
        $roles = Role::all();
        return view('user.edit', compact('user', 'employees', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $authUser = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username,' . $user->id,
            'employee_id' => 'required|exists:employees,id',
            'role_id' => 'required|exists:roles,id',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        // Only admin can change roles
        if ($authUser && $authUser->roles()->where('name', 'admin')->exists()) {
            $user->roles()->sync([$validated['role_id']]);
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }


    public function destroy(User $user)
    {
        $authUser = auth()->user();

        if (!$authUser || !$authUser->hasRole('admin')) {
            return redirect()->route('users.index')->with('error', 'Only admins can delete users.');
        }

        $user->roles()->detach();
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
