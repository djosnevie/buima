<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireOpenCaisseSession
{
    /**
     * Handle an incoming request.
     *
     * Sales (POS, order creation) are EXCLUSIVELY for caissiers.
     * Admins and managers are blocked — they supervise, they do not sell.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Admins and managers are FORBIDDEN from selling
        if ($user && $user->isAdmin()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Les ventes sont réservées aux caissiers. Un manager ou administrateur ne peut pas vendre.'
                ], 403);
            }

            return redirect()->route('dashboard')
                ->with('error', 'Les ventes sont réservées aux caissiers. Vous ne pouvez pas accéder à cette page.');
        }

        // Caissiers MUST have an open cash session before selling
        if ($user && !$user->hasOpenSession()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Veuillez ouvrir une caisse avant de commencer les opérations.'
                ], 403);
            }

            return redirect()->route('pos.index')
                ->with('error', 'Veuillez ouvrir une caisse avant de commencer les opérations de vente.');
        }

        return $next($request);
    }
}
