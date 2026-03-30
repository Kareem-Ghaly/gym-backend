<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated',
            ], 401);
        }

        // Ensure roles are loaded
        if (!$user->relationLoaded('roles')) {
            $user->load('roles');
        }

        if (!$user->hasRole($role)) {
            return response()->json([
                'status' => 'error',
                'message' => 'User does not have the right roles.',
            ], 403);
        }

        return $next($request);
    }
}

