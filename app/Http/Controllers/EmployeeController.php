<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with('user');

        if ($request->has('trashed') && $request->trashed == '1') {
            $query->onlyTrashed();
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('employee_code', 'like', '%'.$request->search.'%')
                  ->orWhere('department', 'like', '%'.$request->search.'%')
                  ->orWhere('designation', 'like', '%'.$request->search.'%')
                  ->orWhere('phone', 'like', '%'.$request->search.'%');
            });
        }

        $employees = $query->latest($request->has('trashed') && $request->trashed == '1' ? 'deleted_at' : 'created_at')
            ->paginate(12)
            ->withQueryString();

        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        return view('employees.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_code' => ['required', 'string', 'max:50', 'unique:employees,employee_code'],
            'staff_name' => ['required', 'string', 'max:150'],
            'login_email' => ['nullable', 'email', 'max:150', 'unique:users,email'],
            'login_username' => ['nullable', 'string', 'max:100', 'unique:users,username'],
            'login_password' => ['nullable', 'string', 'min:6'],
            'phone' => ['nullable', 'string', 'max:20'],
            'department' => ['nullable', 'string', 'max:100'],
            'designation' => ['nullable', 'string', 'max:100'],
            'salary' => ['required', 'numeric', 'min:0'],
            'joining_date' => ['nullable', 'date'],
            'address' => ['nullable', 'string'],
            'status' => ['nullable', 'boolean'],
            'access' => ['nullable', 'array'],
            'biometric_id' => ['nullable', 'string', 'max:50'],
        ]);

        $data['status'] = $request->boolean('status');
        
        $staffRole = Role::where('slug', 'staff')->first();
        $email = $request->login_email ?: (strtolower($data['employee_code']) . '@staff.com');
        $username = $request->login_username ?: strtolower($data['employee_code']);
        $password = $request->login_password ? \Illuminate\Support\Facades\Hash::make($request->login_password) : \Illuminate\Support\Facades\Hash::make('staff123');

        $user = User::create([
            'name' => $request->staff_name,
            'email' => $email,
            'username' => $username,
            'password' => $password,
            'role_id' => $staffRole?->id,
            'status' => $data['status'],
            'access' => $request->input('access', []),
        ]);

        unset($data['staff_name'], $data['login_email'], $data['login_username'], $data['login_password'], $data['access']);
        $data['user_id'] = $user->id;

        Employee::create($data);

        return redirect()->route('employees.index')->with('success', 'Employee created successfully.');
    }

    public function show(Employee $employee)
    {
        $employee->load('user');
        
        // Load recent attendance
        $attendances = \App\Models\EmployeeAttendance::where('employee_id', $employee->id)
            ->latest('attendance_date')
            ->limit(10)
            ->get();
            
        // Load salary slips
        $salarySlips = \App\Models\SalarySlip::where('employee_id', $employee->id)
            ->latest('created_at')
            ->get();
            
        // Load tasks
        $tasks = \App\Models\Task::where('assigned_to', $employee->id)
            ->latest('created_at')
            ->limit(5)
            ->get();
            
        // Analytics
        $totalAttendance = \App\Models\EmployeeAttendance::where('employee_id', $employee->id)->count();
        $presentAttendance = \App\Models\EmployeeAttendance::where('employee_id', $employee->id)->where('status', 'Present')->count();
        $attendancePercentage = $totalAttendance > 0 ? round(($presentAttendance / $totalAttendance) * 100) : 0;
        
        return view('employees.show', compact('employee', 'attendances', 'salarySlips', 'tasks', 'attendancePercentage'));
    }

    public function edit(Employee $employee)
    {
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $data = $request->validate([
            'employee_code' => ['required', 'string', 'max:50', 'unique:employees,employee_code,'.$employee->id],
            'staff_name' => ['required', 'string', 'max:150'],
            'login_email' => [
                'nullable',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($employee->user_id),
            ],
            'login_username' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('users', 'username')->ignore($employee->user_id),
            ],
            'login_password' => ['nullable', 'string', 'min:6'],
            'phone' => ['nullable', 'string', 'max:20'],
            'department' => ['nullable', 'string', 'max:100'],
            'designation' => ['nullable', 'string', 'max:100'],
            'salary' => ['required', 'numeric', 'min:0'],
            'joining_date' => ['nullable', 'date'],
            'address' => ['nullable', 'string'],
            'status' => ['nullable', 'boolean'],
            'access' => ['nullable', 'array'],
            'biometric_id' => ['nullable', 'string', 'max:50'],
            // Bank details for payroll
            'bank_account_no'       => ['nullable', 'string', 'max:30'],
            'bank_ifsc'             => ['nullable', 'string', 'max:15'],
            'bank_name'             => ['nullable', 'string', 'max:100'],
            'account_holder_name'   => ['nullable', 'string', 'max:150'],
        ]);

        $data['status'] = $request->boolean('status');

        $staffRole = Role::where('slug', 'staff')->first();
        $email = $request->login_email ?: ($employee->user?->email ?: (strtolower($data['employee_code']) . '@staff.com'));
        $username = $request->login_username ?: ($employee->user?->username ?: strtolower($data['employee_code']));

        $userData = [
            'name' => $request->staff_name,
            'email' => $email,
            'username' => $username,
            'status' => $data['status'],
            'access' => $request->input('access', []),
        ];

        if ($request->filled('login_password')) {
            $userData['password'] = \Illuminate\Support\Facades\Hash::make($request->login_password);
        }

        if ($employee->user) {
            $employee->user->update($userData);
        } else {
            if (!isset($userData['password'])) {
                $userData['password'] = \Illuminate\Support\Facades\Hash::make('staff123');
            }
            $userData['role_id'] = $staffRole?->id;
            $newUser = User::create($userData);
            $data['user_id'] = $newUser->id;
        }

        unset($data['staff_name'], $data['login_email'], $data['login_username'], $data['login_password'], $data['access']);

        $employee->update($data);

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();

        return back()->with('success', 'Employee deleted successfully.');
    }

    public function restore($id)
    {
        $employee = Employee::onlyTrashed()->findOrFail($id);
        
        if ($employee->user_id) {
            $user = User::onlyTrashed()->where('id', $employee->user_id)->first();
            if ($user) {
                $user->restore();
            }
        }

        $employee->restore();

        return back()->with('success', "Employee restored successfully.");
    }

    public function exportCsv(Request $request)
    {
        $query = Employee::with('user');

        if ($request->filled('search')) {
            $query->where('employee_code', 'like', '%'.$request->search.'%')
                ->orWhere('department', 'like', '%'.$request->search.'%')
                ->orWhere('designation', 'like', '%'.$request->search.'%')
                ->orWhere('phone', 'like', '%'.$request->search.'%');
        }

        $employees = $query->latest()->get();

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=employees_export_'.date('Ymd_His').'.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($employees) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, ['Employee Code', 'Staff Name', 'Login Email', 'Phone', 'Department', 'Designation', 'Salary', 'Joining Date', 'Status']);

            foreach ($employees as $employee) {
                fputcsv($file, [
                    $employee->employee_code,
                    $employee->user?->name ?? '-',
                    $employee->user?->email ?? '-',
                    $employee->phone ?? '-',
                    $employee->department ?? '-',
                    $employee->designation ?? '-',
                    number_format($employee->salary, 2),
                    $employee->joining_date ?? '-',
                    $employee->status ? 'Active' : 'Inactive'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $query = Employee::with('user');

        if ($request->filled('search')) {
            $query->where('employee_code', 'like', '%'.$request->search.'%')
                ->orWhere('department', 'like', '%'.$request->search.'%')
                ->orWhere('designation', 'like', '%'.$request->search.'%')
                ->orWhere('phone', 'like', '%'.$request->search.'%');
        }

        $employees = $query->latest()->get();

        return view('employees.print', compact('employees'));
    }
}
