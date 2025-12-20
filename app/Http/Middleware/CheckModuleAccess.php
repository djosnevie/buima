<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckModuleAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $module): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Super Admin has access to everything for management
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Check establishment modules
        if (!$user->etablissement || !$user->etablissement->hasModule($module)) {
            return redirect()->route('dashboard')->with('error', "Ce module n'est pas activé pour votre établissement.");
        }

        return $next($request);
    }
}
