<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Check if the user is authenticated and banned
        if ($user && $user->status === 'banned') {
            auth('api')->logout();
            
            return response()->json([
                'error' => true,
                'message' => 'You are banned.',
            ], 403);
        }

        return $next($request);
    }
}
