<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $user = auth()->guard('api')->user();
        if (!$user || $user->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized. admin access only.'], 403);
        }
        return $next($request);
    }
}
