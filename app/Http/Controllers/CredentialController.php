<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class CredentialController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('role')->whereHas('role', function ($q) {
            $q->whereIn('slug', ['student', 'staff']);
        });

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

        $users = $query->latest()->paginate(15)->withQueryString();

        return view('credentials.index', compact('users'));
    }

    public function create()
    {
        $roles = \App\Models\Role::whereIn('slug', ['student', 'staff'])->get();
        $students = \App\Models\Student::with(['user', 'course'])->orderBy('first_name')->get();
        $employees = \App\Models\Employee::with(['user'])->orderBy('first_name')->get();

        return view('credentials.create', compact('roles', 'students', 'employees'));
    }

    public function store(Request $request)
    {
        $targetUser = null;
        $student = null;
        $employee = null;

        if ($request->filled('student_id')) {
            $student = \App\Models\Student::find($request->student_id);
            if ($student && $student->user_id) {
                $targetUser = \App\Models\User::find($student->user_id);
            }
        } elseif ($request->filled('employee_id')) {
            $employee = \App\Models\Employee::find($request->employee_id);
            if ($employee && $employee->user_id) {
                $targetUser = \App\Models\User::find($employee->user_id);
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
                'password' => \Illuminate\Support\Facades\Hash::make($request->password),
                'raw_password' => $request->password,
                'role_id' => $request->role_id,
            ]);
            $user = $targetUser;
        } else {
            $user = \App\Models\User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => \Illuminate\Support\Facades\Hash::make($request->password),
                'raw_password' => $request->password,
                'role_id' => $request->role_id,
                'status' => true,
            ]);
        }

        if ($student && !$student->user_id) {
            $student->user_id = $user->id;
            $student->save();
        }

        if ($employee && !$employee->user_id) {
            $employee->user_id = $user->id;
            $employee->save();
        }

        return redirect()->route('credentials.index')->with('success', 'Credential created & account linked successfully.');
    }

    public function edit(User $credential)
    {
        $roles = \App\Models\Role::whereIn('slug', ['student', 'staff'])->get();
        return view('credentials.edit', compact('credential', 'roles'));
    }

    public function update(Request $request, User $credential)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $credential->id,
            'email' => 'nullable|email|max:255|unique:users,email,' . $credential->id,
            'password' => 'nullable|string|min:6',
            'role_id' => 'required|exists:roles,id',
        ]);

        $data = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'role_id' => $request->role_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
            $data['raw_password'] = $request->password;
        }

        $credential->update($data);

        return redirect()->route('credentials.index')->with('success', 'Credential updated successfully.');
    }

    public function destroy(User $credential)
    {
        $credential->delete();
        return redirect()->route('credentials.index')->with('success', 'Credential deleted successfully.');
    }

    public function showPassword($id)
    {
        $user = User::findOrFail($id);
        return response()->json([
            'success' => true,
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'password' => $user->raw_password ?? 'Encrypted / Default Password'
        ]);
    }
}
