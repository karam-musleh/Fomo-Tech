<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MentorUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->guard('api')->user();
        // dd($user->role);

        if (!$user || !in_array($user->role, ['mentor', 'admin'])) {
            return response()->json(['message' => 'Unauthorized. Mentor access only.'], 403);
        }

        return $next($request);
    }
}
