<?php

namespace App\Http\Middleware;

use App\Models\Session;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak ditemukan. Harap login terlebih dahulu.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $session = Session::with('user')
            ->where('token', $token)
            ->first();

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak valid.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        if ($session->logged_out_at !== null) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi sudah diakhiri. Harap login kembali.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        if ($session->expired_at && $session->expired_at->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'Token sudah kadaluwarsa. Harap login kembali.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Inject data user ke request agar bisa diakses di controller
        $request->merge(['auth_user' => $session->user]);
        $request->setUserResolver(fn () => $session->user);

        return $next($request);
    }
}
