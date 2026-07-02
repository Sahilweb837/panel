<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class FirebasePhoneVerificationController extends Controller
{
    private function getFirebaseConfig()
    {
        return [
            'api_key' => env('FIREBASE_API_KEY'),
            'project_id' => env('FIREBASE_PROJECT_ID'),
            'auth_domain' => env('FIREBASE_AUTH_DOMAIN'),
        ];
    }

    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => ['required', 'string', 'max:20'],
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        $phone = $request->phone_number;
        $apiKey = env('FIREBASE_API_KEY');

        if (!$apiKey) {
            return response()->json(['success' => false, 'message' => 'Firebase API key is not configured.']);
        }

        $response = Http::timeout(30)->post("https://identitytoolkit.googleapis.com/v1/accounts:sendOobCode?key={$apiKey}", [
            'phoneNumber' => $phone,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully.',
                'sessionInfo' => $data['sessionInfo'] ?? null,
            ]);
        }

        $error = $response->json('error') ?? [];
        $message = $error['message'] ?? 'Failed to send OTP. Please try again.';

        return response()->json(['success' => false, 'message' => $message]);
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => ['required', 'string', 'max:20'],
            'otp' => ['required', 'string', 'size:6'],
            'session_info' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        $apiKey = env('FIREBASE_API_KEY');

        if (!$apiKey) {
            return response()->json(['success' => false, 'message' => 'Firebase API key is not configured.']);
        }

        $response = Http::timeout(30)->post("https://identitytoolkit.googleapis.com/v1/accounts:signInWithPhoneNumber?key={$apiKey}", [
            'phoneNumber' => $request->phone_number,
            'sessionInfo' => $request->session_info,
            'code' => $request->otp,
        ]);

        if ($response->successful()) {
            $data = $response->json();

            $user = User::where('phone_number', $request->phone_number)->first();

            if (!$user) {
                return response()->json(['success' => false, 'message' => 'No account found with this phone number.']);
            }

            $user->is_phone_verified = true;
            $user->phone_verified_at = now();
            $user->firebase_uid = $data['localId'] ?? null;
            $user->save();

            $userId = $user->id;
            $roleSlug = $user->role?->slug;

            Session::put('user_id', $userId);
            Session::put('user_name', $user->name);
            Session::put('user_role', $user->role?->role_name ?? 'User');
            Session::put('user_role_slug', $roleSlug ?? 'user');

            return response()->json([
                'success' => true,
                'message' => 'Phone verified successfully! Redirecting...',
                'redirect_url' => $roleSlug === 'student' ? route('student.dashboard') : route('staff.dashboard'),
            ]);
        }

        $error = $response->json('error') ?? [];
        $message = $error['message'] ?? 'Invalid OTP. Please try again.';

        return response()->json(['success' => false, 'message' => $message]);
    }
}
