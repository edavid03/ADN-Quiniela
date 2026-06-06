<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->is_admin) {
            if ($request->expectsJson()) {
                abort(403);
            }

            return redirect()
                ->route('dashboard')
                ->with('security_alert', 'No tienes permisos para acceder al panel de administracion.');
        }

        return $next($request);
    }
}
