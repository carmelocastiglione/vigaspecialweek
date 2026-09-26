<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use GuzzleHttp\Client;
use Throwable;

class SocialAuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')
            ->stateless()
            ->setHttpClient(
                new Client([
                    'verify' => app()->environment('production'), // Disable SSL verification in local/staging
                ])
            )
            ->user();
        } catch (Throwable $e) {
            Log::error('Google OAuth driver error', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);
            return redirect('/login')->with('error', 'Errore durante il login con Google.');
        }

        // Find the user by email (only allow pre-registered users)
        $user = User::where('email', $googleUser->getEmail())->first();

        if (!$user) {
            Log::warning('Unauthorized Google login attempt', [
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
            ]);
            return redirect('/login')->with('error', 'Email non autorizzata per il login con Google.');
        }

        // Link Google account if not already linked
        if (!$user->google_id) {
            $user->update(['google_id' => $googleUser->getId()]);
        }

        Auth::login($user);

        return redirect()->intended('/dashboard');
    }
}
