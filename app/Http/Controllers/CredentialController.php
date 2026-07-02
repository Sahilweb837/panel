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
}
