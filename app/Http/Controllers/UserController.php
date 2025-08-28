<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
{
    if (!auth()->user()->hasRole('admin')) {
        abort(403, 'Access denied. Admin role required.');
    }
    
    $query = User::with('employee', 'roles');

    if ($request->search) {
        $query->where('username', 'like', '%' . $request->search . '%')
              ->orWhereHas('employee', function ($q) use ($request) {
                  $q->where('first_name', 'like', '%' . $request->search . '%')
                    ->orWhere('last_name', 'like', '%' . $request->search . '%');
              });
    }

    $users = $query->paginate(10);
    $employees = Employee::all();
    $roles = Role::all();

    return view('user.index', compact('users', 'employees', 'roles'));
}


    public function create()
    {
        $employees = Employee::whereDoesntHave('user')->get(); 
        $roles = Role::all();
        return view('user.create', compact('employees', 'roles'));
    }

    public function store(Request $request)
{
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

    $newUser = User::create([
        'employee_id' => $validated['employee_id'],
        'username' => $validated['username'],
        'name' => $fullName,
        'password' => Hash::make($validated['password']),
    ]);

    $newUser->roles()->attach($validated['role_id']);

    return redirect()->route('users.index')->with('success', 'User created successfully and can now log in.');
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
        $validated = $request->validate([
            'username' => 'required|string|unique:users,username,' . $user->id,
            'role_id' => 'required|exists:roles,id',
            'password' => 'nullable|string|min:8', // No 'confirmed' rule
        ]);

        // Log submitted vs current for debugging
    \Log::info('Role update attempt', [
        'user_id' => $user->id,
        'submitted_role_id' => $validated['role_id'],
        'current_role_ids_before' => $user->roles->pluck('id')->toArray(),
    ]);

        $updateData = [
            'username' => $validated['username'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = $validated['password']; // Will be auto-hashed via mutator
        }

        $user->update($updateData);

        $user->roles()->sync([$validated['role_id']]);

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
