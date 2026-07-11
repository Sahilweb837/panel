<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class VerificationController extends Controller
{
    public function verify(Request $request, $id, $hash)
    {
        if (! $request->hasValidSignature()) {
            return redirect()->route('login')->withErrors(['email' => 'The verification link is invalid or has expired.']);
        }

        $user = User::findOrFail($id);

        if (! hash_equals((string) $hash, sha1($user->email))) {
            return redirect()->route('login')->withErrors(['email' => 'Invalid verification link.']);
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login')->with('success', 'Your email is already verified. You can now log in.');
        }

        $user->markEmailAsVerified();

        return redirect()->route('login')->with('success', 'Your email has been successfully verified! You can now log in.');
    }
}
