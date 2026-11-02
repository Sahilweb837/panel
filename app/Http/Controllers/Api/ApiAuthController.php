<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Student;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ApiAuthController extends Controller
{
    /**
     * Authenticate via API and issue Sanctum token.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login'    => 'required|string',
            'password' => 'required|string',
            'device_name' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors()
            ], 422);
        }

        $login = trim($request->input('login'));
        $password = $request->input('password');

        // Locate user by email, username, or admission / employee code
        $user = User::with('role')->where(function ($q) use ($login) {
            $q->where('email', $login)
              ->orWhere('username', $login);
        })->first();

        if (!$user) {
            $cleanLogin = str_ireplace(['NT-ENR-', 'NT-STAFF-', 'NT-'], '', $login);
            $student = Student::where('admission_no', $login)
                ->orWhere('roll_no', $login)
                ->orWhereRaw("REPLACE(REPLACE(admission_no, 'NT-ENR-', ''), 'NT-', '') = ?", [$cleanLogin])
                ->first();

            if ($student && $student->user_id) {
                $user = User::with('role')->find($student->user_id);
            } else {
                $employee = Employee::where('employee_code', $login)
                    ->orWhereRaw("REPLACE(REPLACE(employee_code, 'NT-STAFF-', ''), 'NT-', '') = ?", [$cleanLogin])
                    ->first();
                if ($employee && $employee->user_id) {
                    $user = User::with('role')->find($employee->user_id);
                }
            }
        }

        if (!$user || !Hash::check($password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid login credentials.',
            ], 401);
        }

        if (!$user->status) {
            return response()->json([
                'success' => false,
                'message' => 'Account is inactive or disabled. Contact administrator.',
            ], 403);
        }

        // Determine token abilities based on role
        $roleSlug = strtolower($user->role?->slug ?? 'user');
        $abilities = ['*'];

        if ($roleSlug === 'student') {
            $abilities = ['student:read', 'student:portal'];
        } elseif ($roleSlug === 'staff') {
            $abilities = ['staff:read', 'staff:write'];
        }

        $deviceName = $request->input('device_name', 'API_Client_'.now()->format('Ymd_His'));
        $token = $user->createToken($deviceName, $abilities)->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Authentication successful.',
            'token_type' => 'Bearer',
            'token' => $token,
            'user' => [
                'id'       => $user->id,
                'name'     => $user->name,
                'email'    => $user->email,
                'username' => $user->username,
                'role'     => $user->role?->role_name ?? ucfirst($roleSlug),
                'role_slug'=> $roleSlug,
                'avatar'   => $user->profile_pic ? asset('uploads/profiles/'.$user->profile_pic) : null,
            ]
        ]);
    }

    /**
     * Get details of currently authenticated user.
     */
    public function me(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $user->load('role');

        return response()->json([
            'success' => true,
            'user' => [
                'id'        => $user->id,
                'name'      => $user->name,
                'email'     => $user->email,
                'username'  => $user->username,
                'role'      => $user->role?->role_name ?? 'User',
                'role_slug' => $user->role?->slug ?? 'user',
                'access'    => $user->access ?? [],
                'status'    => (bool)$user->status,
            ]
        ]);
    }

    /**
     * Revoke current token / logout.
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Token revoked and logged out successfully.'
        ]);
    }
}

