<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class SocialAuthController extends Controller
{
    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback(): RedirectResponse
    {
        $frontendUrl = rtrim(config('app.frontend_url'), '/');

        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (Throwable) {
            return redirect("{$frontendUrl}/auth/google/callback?error=google_auth_failed");
        }

        if (! $googleUser->getEmail()) {
            return redirect("{$frontendUrl}/auth/google/callback?error=google_email_missing");
        }

        $user = User::where('google_id', $googleUser->getId())->first();

        if (! $user) {
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'email_verified_at' => $user->email_verified_at ?? now(),
                ]);
            } else {
                $user = User::create([
                    'name' => $googleUser->getName() ?? strstr($googleUser->getEmail(), '@', true),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => Str::password(32),
                    'email_verified_at' => now(),
                ]);
            }
        } elseif ($googleUser->getAvatar()) {
            $user->update(['avatar' => $googleUser->getAvatar()]);
        }

        Auth::login($user);
        request()->session()->regenerate();

        return redirect("{$frontendUrl}/auth/google/callback");
    }
}
