<?php

namespace App\Http\Middleware;

use Closure;
use App\Helpers\ApiHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
        if ($user && $user->status === 'Banned') {
            Log::info("message: ". auth()->user()->email);
            auth()->logout();

           return ApiHelper::output(trans('app.banned'), 0);
        }

        return $next($request);
    }
}
