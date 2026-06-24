<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    public function register(string $name, string $email, string $password): User
    {
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);

        Auth::login($user);
        request()->session()->regenerate();

        return $user;
    }

    public function login(string $email, string $password): User | array
    {
        if (! Auth::attempt(['email' => $email, 'password' => $password])) {
            return  ['message' => 'Invalid credentials', 'code' => 401, 'errors' => ['email' => 'Invalid credentials']];
        }

        request()->session()->regenerate();

        return Auth::user();
    }

    public function logout(): void
    {
        Auth::guard('web')->logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }

    public function me(): User
    {
        return Auth::user();
    }
}
