<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeRequest;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(Request $request): View
    {
        $employees = Employee::with('user')
            ->when($request->search, fn ($query, $search) => $query->where(function ($query) use ($search): void {
                $query->where('employee_code', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($user) => $user->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
            }))
            ->when($request->status, fn ($query, $status) => $query->where('is_active', $status === 'active'))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.employees.index', compact('employees'));
    }

    public function create(): View
    {
        return view('admin.employees.create');
    }

    public function store(EmployeeRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request): void {
            $data = $request->validated();
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => 'employee',
                'is_active' => $request->boolean('is_active'),
            ]);

            Employee::create([
                'user_id' => $user->id,
                'employee_code' => $this->nextEmployeeCode(),
                'phone' => $data['phone'] ?? null,
                'department' => $data['department'] ?? null,
                'designation' => $data['designation'] ?? null,
                'is_active' => $request->boolean('is_active'),
            ]);
        });

        return redirect()->route('admin.employees.index')->with('success', 'Employee was created.');
    }

    public function edit(Employee $employee): View
    {
        $employee->load('user');

        return view('admin.employees.edit', compact('employee'));
    }

    public function status(Employee $employee): RedirectResponse
    {
        $isActive = ! $employee->is_active;
        $employee->update(['is_active' => $isActive]);
        $employee->user?->update(['is_active' => $isActive]);

        return back()->with('success', 'Employee status was updated.');
    }

    public function update(EmployeeRequest $request, Employee $employee): RedirectResponse
    {
        DB::transaction(function () use ($request, $employee): void {
            $data = $request->validated();
            $employee->update([
                'phone' => $data['phone'] ?? null,
                'department' => $data['department'] ?? null,
                'designation' => $data['designation'] ?? null,
                'is_active' => $request->boolean('is_active'),
            ]);

            $userData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'is_active' => $request->boolean('is_active'),
            ];
            if (! empty($data['password'])) {
                $userData['password'] = $data['password'];
            }
            $employee->user->update($userData);
        });

        return redirect()->route('admin.employees.index')->with('success', 'Employee was updated.');
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        if ($employee->orders()->exists()) {
            return back()->with('error', 'Employees with order history cannot be deleted. Deactivate the employee instead.');
        }

        DB::transaction(function () use ($employee): void {
            $user = $employee->user;
            $employee->delete();
            $user?->delete();
        });

        return redirect()->route('admin.employees.index')->with('success', 'Employee was deleted.');
    }

    private function nextEmployeeCode(): string
    {
        $number = ((int) Employee::max('id')) + 1;

        do {
            $code = sprintf('EMP%04d', $number++);
        } while (Employee::where('employee_code', $code)->exists());

        return $code;
    }
}