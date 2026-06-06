<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Employee;
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
        });
    }

    /**
     * Display all sub-admins and employees
     */
    public function index(Request $request)
    {
        $query = User::with('role', 'employee');

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
                $q->select('id')->from('roles')->whereIn('slug', ['admin', 'staff']);
            })
            ->latest($request->has('trashed') && $request->trashed == '1' ? 'deleted_at' : 'created_at')
            ->paginate(12)
            ->withQueryString();

        return view('sub_admins.index', compact('users'));
    }

    /**
     * Show form to create new sub-admin or employee
     */
    public function create()
    {
        $roles = Role::whereIn('slug', ['admin', 'staff'])->get();
        return view('sub_admins.create', compact('roles'));
    }

    /**
     * Store new sub-admin or employee
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'role' => ['required', 'in:admin,staff'],
            'status' => ['required', 'boolean'],
            'access' => ['nullable', 'array'],
        ]);

        // Get role
        $role = Role::where('slug', $data['role'])->first();

        // Create user
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
            'role_id' => $role->id,
            'status' => $data['status'],
            'access' => $request->input('access', []),
        ]);

        // If staff role, create employee record
        if ($data['role'] === 'staff') {
            Employee::create([
                'user_id' => $user->id,
                'employee_code' => 'EMP-'.$user->id,
                'designation' => 'Staff Member',
                'status' => $data['status'],
            ]);
        }

        return redirect()->route('sub-admins.index')->with('success', ucfirst($data['role']).' member created successfully.');
    }

    /**
     * Show form to edit new sub-admin or employee
     */
    public function edit(User $subAdmin)
    {
        $roles = Role::whereIn('slug', ['admin', 'staff'])->get();
        return view('sub_admins.edit', compact('subAdmin', 'roles'));
    }

    /**
     * Update sub-admin or employee
     */
    public function update(Request $request, User $subAdmin)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email,'.$subAdmin->id],
            'username' => ['required', 'string', 'max:50', 'unique:users,username,'.$subAdmin->id],
            'role' => ['required', 'in:admin,staff'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'status' => ['required', 'boolean'],
            'access' => ['nullable', 'array'],
        ]);

        $role = Role::where('slug', $data['role'])->first();

        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'username' => $data['username'],
            'role_id' => $role->id,
            'status' => $data['status'],
            'access' => $request->input('access', []),
        ];

        if ($data['password']) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $subAdmin->update($updateData);

        // Manage employee profile lifecycle based on role selection
        if ($data['role'] === 'staff') {
            if (!$subAdmin->employee) {
                Employee::create([
                    'user_id' => $subAdmin->id,
                    'employee_code' => 'EMP-'.$subAdmin->id,
                    'designation' => 'Staff Member',
                    'status' => $data['status'],
                ]);
            } else {
                $subAdmin->employee->update(['status' => $data['status']]);
            }
        } elseif ($data['role'] === 'admin') {
            if ($subAdmin->employee) {
                $subAdmin->employee->delete();
            }
        }

        return redirect()->route('sub-admins.index')->with('success', 'User updated successfully.');
    }

    /**
     * Delete sub-admin or employee
     */
    public function destroy(User $subAdmin)
    {
        $this->authorize('delete', $subAdmin);

        $name = $subAdmin->name;
        
        // Delete associated employee record if exists
        if ($subAdmin->employee) {
            $subAdmin->employee->delete();
        }

        $subAdmin->delete();

        return redirect()->route('sub-admins.index')->with('success', "User '$name' deleted successfully.");
    }

    /**
     * Display soft-deleted sub-admins and employees
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
                $q->select('id')->from('roles')->whereIn('slug', ['admin', 'staff']);
            })
            ->latest('deleted_at')->paginate(12)->withQueryString();

        return view('sub_admins.trash', compact('users'));
    }

    /**
     * Restore soft-deleted sub-admin or employee
     */
    public function restore($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        
        // Restore associated employee record if exists
        $employee = Employee::onlyTrashed()->where('user_id', $user->id)->first();
        if ($employee) {
            $employee->restore();
        }

        $user->restore();

        return redirect()->route('sub-admins.trash')->with('success', "User '{$user->name}' restored successfully.");
    }

}
