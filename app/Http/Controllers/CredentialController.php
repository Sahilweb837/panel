<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\Employee;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CredentialController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['role', 'student', 'employee'])->whereHas('role', function ($q) {
            $q->whereIn('slug', ['student', 'staff']);
        });

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                  ->orWhere('email', 'like', '%'.$search.'%')
                  ->orWhere('username', 'like', '%'.$search.'%')
                  ->orWhereHas('student', function($sq) use ($search) {
                      $sq->where('admission_no', 'like', '%'.$search.'%')
                        ->orWhere('roll_no', 'like', '%'.$search.'%')
                        ->orWhere('first_name', 'like', '%'.$search.'%')
                        ->orWhere('last_name', 'like', '%'.$search.'%');
                  });
            });
        }

        if ($request->filled('role')) {
            $query->whereHas('role', function ($q) use ($request) {
                $q->where('slug', $request->role);
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        return view('credentials.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::whereIn('slug', ['student', 'staff'])->get();
        $students = Student::with(['user', 'course'])->orderBy('first_name')->get();
        $employees = Employee::with(['user'])->get()->sortBy(function($emp) {
            return strtolower($emp->user->name ?? '');
        });

        return view('credentials.create', compact('roles', 'students', 'employees'));
    }

    public function store(Request $request)
    {
        $targetUser = null;
        $student = null;
        $employee = null;

        if ($request->filled('student_id')) {
            $student = Student::find($request->student_id);
            if ($student && $student->user_id) {
                $targetUser = User::find($student->user_id);
            }
        } elseif ($request->filled('employee_id')) {
            $employee = Employee::find($request->employee_id);
            if ($employee && $employee->user_id) {
                $targetUser = User::find($employee->user_id);
            }
        }

        $userId = $targetUser ? $targetUser->id : null;

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $userId,
            'email' => 'nullable|email|max:255|unique:users,email,' . $userId,
            'password' => 'required|string|min:6',
            'role_id' => 'required|exists:roles,id',
        ]);

        if ($targetUser) {
            $targetUser->update([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'raw_password' => $request->password,
                'role_id' => $request->role_id,
                'status' => true,
            ]);
            $user = $targetUser;
        } else {
            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'raw_password' => $request->password,
                'role_id' => $request->role_id,
                'status' => true,
            ]);
        }

        if ($student) {
            $student->user_id = $user->id;
            $student->portal_active = true;
            $student->save();
        }

        if ($employee && !$employee->user_id) {
            $employee->user_id = $user->id;
            $employee->save();
        }

        return redirect()->route('credentials.index')->with('success', 'Credential created & active portal access linked successfully.');
    }

    public function edit(User $credential)
    {
        $roles = Role::whereIn('slug', ['student', 'staff'])->get();
        return view('credentials.edit', compact('credential', 'roles'));
    }

    public function update(Request $request, User $credential)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $credential->id,
            'email' => 'nullable|email|max:255|unique:users,email,' . $credential->id,
            'password' => 'nullable|string|min:6',
            'role_id' => 'nullable|exists:roles,id',
        ]);

        $data = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'role_id' => $request->role_id ?: $credential->role_id,
            'status' => true,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
            $data['raw_password'] = $request->password;
        }

        $credential->update($data);

        // Sync portal_active if student
        if ($credential->student) {
            $credential->student->update(['portal_active' => true]);
        }

        return redirect()->route('credentials.index')->with('success', 'Credential updated and portal access activated.');
    }

    public function togglePortal(User $credential)
    {
        if ($credential->student) {
            $newStatus = !$credential->student->portal_active;
            $credential->student->update(['portal_active' => $newStatus]);
            $credential->update(['status' => $newStatus]);
            $msg = $newStatus ? 'Student portal access enabled successfully.' : 'Student portal access disabled.';
            return back()->with('success', $msg);
        }

        $newStatus = !$credential->status;
        $credential->update(['status' => $newStatus]);
        return back()->with('success', 'User access status updated.');
    }

    public function impersonate(User $credential)
    {
        // Activate portal & status
        $credential->update(['status' => true]);
        if ($credential->student) {
            $credential->student->update(['portal_active' => true]);
        }

        // Keep track of admin ID for returning
        if (!session()->has('admin_impersonator_id')) {
            session(['admin_impersonator_id' => session('user_id')]);
        }

        // Complete login session as student or staff
        app(AuthController::class)->completeLogin($credential, $credential->role?->slug ?? 'student');

        if ($credential->role?->slug === 'student') {
            return redirect()->route('student.dashboard')->with('success', 'Directly logged in to Student Dashboard for ' . $credential->name . ' (' . ($credential->student?->admission_no ?? $credential->username) . ').');
        } elseif ($credential->role?->slug === 'staff') {
            return redirect()->route('staff.dashboard')->with('success', 'Logged in as staff ' . $credential->name);
        }

        return redirect()->route('dashboard');
    }

    public function destroy(User $credential)
    {
        $credential->delete();
        return redirect()->route('credentials.index')->with('success', 'Credential deleted successfully.');
    }
}

