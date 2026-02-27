<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth.token' => \App\Http\Middleware\AuthenticateToken::class,
        ]);

        // Semua route adalah REST API, tidak memerlukan CSRF token
        $middleware->validateCsrfTokens(except: ['*']);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // 404 – Route tidak ditemukan
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            return response()->json([
                'success' => false,
                'message' => 'Endpoint tidak ditemukan.',
            ], 404);
        });

        // 405 – HTTP method tidak diizinkan
        $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request) {
            return response()->json([
                'success' => false,
                'message' => 'HTTP method tidak diizinkan untuk endpoint ini.',
            ], 405);
        });

        // 500 – Unhandled server error (sembunyikan detail di production)
        $exceptions->render(function (\Throwable $e, Request $request) {
            $debug = config('app.debug');

            return response()->json([
                'success' => false,
                'message' => $debug ? $e->getMessage() : 'Terjadi kesalahan pada server. Silakan coba lagi.',
                ...($debug ? ['trace' => collect($e->getTrace())->take(5)->toArray()] : []),
            ], 500);
        });

    })->create();
