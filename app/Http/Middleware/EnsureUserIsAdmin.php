<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Admin middleware allows Admins AND Super Admins
        if (!$request->user() || !$request->user()->isAdmin()) {
            // Redirect back with error message instead of 403
            return redirect()->back()->with('error', 'Accès réservé aux administrateurs.');
        }

        return $next($request);
    }
}
