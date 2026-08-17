<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // User is not authenticated or is not an admin
        if (!$user || !$user->isAdmin()) {
            return response()->json([
                'message' => 'এই পেজ অ্যাক্সেস করার অনুমতি আপনার নেই।',
            ], 403);
        }

        // User account is not active
        if (!$user->isActive()) {
            return response()->json([
                'message' => 'আপনার অ্যাকাউন্ট সক্রিয় নয়।',
            ], 403);
        }

        return $next($request);
    }
}