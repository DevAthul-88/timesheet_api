<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\AuthenticationException;

class AuthService
{
    /**
     * Register a new user.
     *
     * @param array $data
     * @return User
     */
    public function registerUser(array $data): User
    {
        return User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);
    }

    /**
     * Attempt to authenticate a user.
     *
     * @param string $email
     * @param string $password
     * @return User
     * @throws AuthenticationException
     */
    public function attemptLogin(string $email, string $password): User
    {
        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            return Auth::user();
        }

        throw new AuthenticationException('Invalid credentials');
    }

    /**
     * Generate access token for user.
     *
     * @param User $user
     * @return string
     */
    public function createAccessToken(User $user): string
    {
        return $user->createToken('auth_token')->accessToken;
    }

    /**
     * Revoke user's current token.
     *
     * @param User $user
     * @return bool
     */
    public function revokeToken(User $user): bool
    {
        return $user->token()->revoke();
    }

    public function findUserByEmail(string $email)
    {
        return User::where('email', $email)->first();
    }
}
