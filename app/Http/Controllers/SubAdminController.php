<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Employee;
use App\Models\Student;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SubAdminController extends Controller
{
    // Only superadmin can manage sub-admins
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $roleSlug = session('user_role_slug');
            if (!in_array($roleSlug, ['super-admin', 'superadmin', 'root-admin'])) {
                return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
            }
            return $next($request);
        })->except(['showPassword', 'updatePassword']);
    }

    /**
     * Display all sub-admins, employees, and students
     */
    public function index(Request $request)
    {
        $query = User::with('role', 'employee', 'student');

        if ($request->has('trashed') && $request->trashed == '1') {
            $query->onlyTrashed();
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                  ->orWhere('email', 'like', '%'.$request->search.'%')
                  ->orWhere('username', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->filled('role')) {
            $query->whereHas('role', function ($q) use ($request) {
                $q->where('slug', $request->role);
            });
        }

        $users = $query->where('id', '!=', session('user_id'))
            ->whereIn('role_id', function ($q) {
                $q->select('id')->from('roles')->whereIn('slug', ['admin', 'staff', 'student']);
            })
            ->latest($request->has('trashed') && $request->trashed == '1' ? 'deleted_at' : 'created_at')
            ->paginate(12)
            ->withQueryString();

        return view('sub_admins.index', compact('users'));
    }

    /**
     * Show form to create new sub-admin, employee, or student
     */
    public function create()
    {
        $roles = Role::whereIn('slug', ['admin', 'staff', 'student'])->get();
        $courses = Course::where('status', true)->orderBy('name')->get();
        return view('sub_admins.create', compact('roles', 'courses'));
    }

    /**
     * Store new account (admin/staff/student)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'email'       => ['required', 'email', 'unique:users,email'],
            'username'    => ['required', 'string', 'max:50', 'unique:users,username'],
            'password'    => ['required', 'string', 'min:6', 'confirmed'],
            'role'        => ['required', 'in:admin,staff,student'],
            'status'      => ['required', 'boolean'],
            'access'      => ['nullable', 'array'],
            // Student-specific optional fields
            'first_name'  => ['nullable', 'string', 'max:100'],
            'last_name'   => ['nullable', 'string', 'max:100'],
            'admission_no'=> ['nullable', 'string', 'max:50'],
            'course_id'   => ['nullable', 'exists:courses,id'],
            'phone'       => ['nullable', 'string', 'max:20'],
        ]);

        $role = Role::where('slug', $data['role'])->first();

        // Create user — store both hashed and raw password
        $user = User::create([
            'name'         => $data['name'],
            'email'        => $data['email'],
            'username'     => $data['username'],
            'password'     => Hash::make($data['password']),
            'raw_password' => $data['password'],
            'role_id'      => $role->id,
            'status'       => $data['status'],
            'access'       => $request->input('access', []),
        ]);

        // Role-specific record creation
        if ($data['role'] === 'staff') {
            Employee::create([
                'user_id'      => $user->id,
                'employee_code'=> 'EMP-'.$user->id,
                'designation'  => 'Staff Member',
                'status'       => $data['status'],
            ]);
        } elseif ($data['role'] === 'student') {
            Student::create([
                'user_id'       => $user->id,
                'first_name'    => $data['first_name'] ?? $data['name'],
                'last_name'     => $data['last_name'] ?? '',
                'email'         => $data['email'],
                'phone'         => $data['phone'] ?? null,
                'admission_no'  => $data['admission_no'] ?? 'ADM-'.$user->id,
                'course_id'     => $data['course_id'] ?? null,
                'admission_date'=> now()->toDateString(),
                'status'        => $data['status'],
            ]);
        }

        return redirect()->route('sub-admins.index')->with('success', ucfirst($data['role']).' account created successfully.');
    }

    /**
     * Show form to edit account
     */
    public function edit(User $subAdmin)
    {
        $roles = Role::whereIn('slug', ['admin', 'staff', 'student'])->get();
        $courses = Course::where('status', true)->orderBy('name')->get();
        return view('sub_admins.edit', compact('subAdmin', 'roles', 'courses'));
    }

    /**
     * Update account
     */
    public function update(Request $request, User $subAdmin)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'unique:users,email,'.$subAdmin->id],
            'username' => ['required', 'string', 'max:50', 'unique:users,username,'.$subAdmin->id],
            'role'     => ['required', 'in:admin,staff,student'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'status'   => ['required', 'boolean'],
            'access'   => ['nullable', 'array'],
        ]);

        $role = Role::where('slug', $data['role'])->first();

        $updateData = [
            'name'     => $data['name'],
            'email'    => $data['email'],
            'username' => $data['username'],
            'role_id'  => $role->id,
            'status'   => $data['status'],
            'access'   => $request->input('access', []),
        ];

        if (!empty($data['password'])) {
            $updateData['password']     = Hash::make($data['password']);
            $updateData['raw_password'] = $data['password'];
        }

        $subAdmin->update($updateData);

        // Manage role-specific profile lifecycle
        if ($data['role'] === 'staff') {
            if (!$subAdmin->employee) {
                Employee::create([
                    'user_id'      => $subAdmin->id,
                    'employee_code'=> 'EMP-'.$subAdmin->id,
                    'designation'  => 'Staff Member',
                    'status'       => $data['status'],
                ]);
            } else {
                $subAdmin->employee->update(['status' => $data['status']]);
            }
        } elseif ($data['role'] === 'admin') {
            if ($subAdmin->employee) {
                $subAdmin->employee->delete();
            }
        }

        return redirect()->route('sub-admins.index')->with('success', 'Account updated successfully.');
    }

    /**
     * View raw/plaintext password for a user (AJAX) - Authorized Admins & SubAdmins
     */
    public function showPassword(User $user)
    {
        $roleSlug = session('user_role_slug');
        if (!in_array($roleSlug, ['super-admin', 'superadmin', 'root-admin', 'admin', 'subadmin', 'sub-admin'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $user->load(['student.course', 'employee']);
        
        $phone = null;
        $course = null;
        if ($user->student) {
            $phone = $user->student->phone;
            $course = $user->student->course?->name;
        } elseif ($user->employee) {
            $phone = $user->employee->phone;
        }

        return response()->json([
            'id'       => $user->id,
            'name'     => $user->name,
            'email'    => $user->email,
            'username' => $user->username,
            'password' => $user->raw_password ?? '(Not recorded — was set before this feature)',
            'phone'    => $phone,
            'course'   => $course,
        ]);
    }

    /**
     * Update user password (AJAX / POST) - Authorized Admins & SubAdmins
     */
    public function updatePassword(Request $request, User $user)
    {
        $roleSlug = session('user_role_slug');
        if (!in_array($roleSlug, ['super-admin', 'superadmin', 'root-admin', 'admin', 'subadmin', 'sub-admin'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'password' => 'required|string|min:6',
        ]);

        $newPassword = $request->input('password');
        $user->password = Hash::make($newPassword);
        $user->raw_password = $newPassword;
        $user->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Password updated successfully.',
                'password' => $newPassword
            ]);
        }

        return redirect()->back()->with('success', 'Password updated successfully.');
    }

    /**
     * Delete account
     */
    public function destroy(User $subAdmin)
    {
        $name = $subAdmin->name;
        
        if ($subAdmin->employee) {
            $subAdmin->employee->delete();
        }
        if ($subAdmin->student) {
            $subAdmin->student->delete();
        }

        $subAdmin->delete();

        return redirect()->route('sub-admins.index')->with('success', "User '$name' deleted successfully.");
    }

    /**
     * Display soft-deleted accounts
     */
    public function trash(Request $request)
    {
        $query = User::onlyTrashed()->with(['role', 'employee' => function($q) {
            $q->withTrashed();
        }]);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%')
                ->orWhere('email', 'like', '%'.$request->search.'%')
                ->orWhere('username', 'like', '%'.$request->search.'%');
        }

        $users = $query->whereIn('role_id', function ($q) {
                $q->select('id')->from('roles')->whereIn('slug', ['admin', 'staff', 'student']);
            })
            ->latest('deleted_at')->paginate(12)->withQueryString();

        return view('sub_admins.trash', compact('users'));
    }

    /**
     * Restore soft-deleted account
     */
    public function restore($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        
        $employee = Employee::onlyTrashed()->where('user_id', $user->id)->first();
        if ($employee) {
            $employee->restore();
        }

        $student = Student::onlyTrashed()->where('user_id', $user->id)->first();
        if ($student) {
            $student->restore();
        }

        $user->restore();

        return redirect()->route('sub-admins.trash')->with('success', "User '{$user->name}' restored successfully.");
    }
}
