<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    // Show all employees
    public function index()
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Access denied. Admin role required.');
        }
        
        $employees = Employee::all();
        return view('employee.index', compact('employees'));
    }

    // Show form to create new employee
    public function create()
    {
        return view('employee.create');
    }

    // Store a new employee
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:50',
            'middle_name' => 'nullable|string|max:50',
            'last_name' => 'required|string|max:50',
            'office' => 'required|string|max:100',
        ]);

        Employee::create($request->all());

        return redirect()->route('employees.index')->with('success', 'Employee added successfully.');
    }

    // Show single employee (optional)
    public function show($id)
    {
        $employee = Employee::findOrFail($id);
        return view('employee.show', compact('employee'));
    }

    // Show form to edit an existing employee
    public function edit(Employee $employee)
    {
        return view('employee.edit', compact('employee'));
    }

    // Update the employee record
    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'first_name' => 'required|string|max:50',
            'middle_name' => 'nullable|string|max:50',
            'last_name' => 'required|string|max:50',
            'office' => 'required|string|max:100',
        ]);

        $employee->update($request->all());

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }

    // Delete the employee
    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'Employee deleted.');
    }
}
