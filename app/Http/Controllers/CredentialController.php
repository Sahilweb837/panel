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
        return view('credentials.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'nullable|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role_id' => 'required|exists:roles,id',
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'raw_password' => $request->password,
            'role_id' => $request->role_id,
        ]);

        return redirect()->route('credentials.index')->with('success', 'Credential created successfully.');
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
}
