<?php

namespace App\Services;

use App\Models\Session;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthService
{
    public function login(string $email, string $password): array
    {
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return [
                'success' => false,
                'message' => 'Email atau password salah.',
            ];
        }

        // Logout session lama milik user ini jika ada (single active session)
        Session::where('user_id', $user->id)
            ->whereNull('logged_out_at')
            ->update(['logged_out_at' => now()]);

        $session = Session::create([
            'user_id'    => $user->id,
            'token'      => Str::random(60),
            'expired_at' => now()->addHours(1),
        ]);

        return [
            'success' => true,
            'data'    => [
                'token'      => $session->token,
                'expired_at' => $session->expired_at->toISOString(),
                'user'       => [
                    'id'        => $user->id,
                    'user_name' => $user->user_name,
                    'email'     => $user->email,
                ],
            ],
        ];
    }

    public function logout(string $token): void
    {
        Session::where('token', $token)
            ->whereNull('logged_out_at')
            ->update(['logged_out_at' => now()]);
    }

    public function me(User $user): array
    {
        return [
            'id'        => $user->id,
            'user_name' => $user->user_name,
            'email'     => $user->email,
        ];
    }
}
