<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the callback from Google.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Check if user exists with this Google ID
            $user = User::where('google_id', $googleUser->id)->first();

            if ($user) {
                // User exists with this Google ID, log them in
                Auth::login($user);

                return redirect()->intended('/dashboard');
            }

            // Check if user exists with this email
            $user = User::where('email', $googleUser->email)->first();

            if ($user) {
                // User exists but hasn't linked Google yet
                // Link the Google ID to this account
                $user->update([
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                ]);

                Auth::login($user);

                return redirect()->intended('/dashboard');
            }

            // Create new user
            $user = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'google_id' => $googleUser->id,
                'avatar' => $googleUser->avatar,
                'email_verified_at' => now(), // Google emails are verified
                'password' => Hash::make(Str::random(32)), // Random password
            ]);

            Auth::login($user);

            return redirect()->intended('/dashboard');
        } catch (\Exception $e) {
            \Log::error('Google OAuth Error: ' . $e->getMessage());

            return redirect('/login')->with('error', 'Unable to login with Google. Please try again.');
        }
    }
}

