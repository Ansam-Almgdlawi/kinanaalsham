<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthenticationService
{
    /**
     * Authenticate a user and return a token.
     *
     * @param array $credentials
     * @return array
     * @throws ValidationException
     */
    public function login(array $credentials): array
    {
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['البيانات المدخلة غير صحيحة.'],
            ]);
        }

        // Check if user is active
        if ($user->status !== 'active') {
            throw ValidationException::withMessages([
                'email' => ['هذا الحساب غير نشط. يرجى التواصل مع الإدارة.'],
            ]);
        }

        // Check if user is a volunteer
        if (!$user->isVolunteer()) {
            throw ValidationException::withMessages([
                'email' => ['هذا الحساب غير مصرح له بالدخول إلى تطبيق المتطوعين.'],
            ]);
        }

        // Revoke all existing tokens
        $user->tokens()->delete();

        // Create new token
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }

    /**
     * Logout a user by revoking tokens.
     *
     * @param User $user
     * @return bool
     */
    public function logout(User $user): bool
    {
        return $user->tokens()->delete() > 0;
    }
}
