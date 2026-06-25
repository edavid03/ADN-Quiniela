<?php

use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\EnsureUserIsNotAdmin;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
            'not_admin' => EnsureUserIsNotAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (TokenMismatchException $exception, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Tu sesion expiro. Recarga el formulario e intenta nuevamente.',
                ], 419);
            }

            $redirect = $request->is('login')
                ? redirect()->route('login')
                : back();

            return $redirect
                ->withInput($request->except('_token', 'password', 'password_confirmation'))
                ->withErrors([
                    'session' => 'Tu sesion expiro. Recarga el formulario e intenta iniciar sesion nuevamente.',
                ]);
        });
    })->create();
