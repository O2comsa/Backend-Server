<?php

namespace App\Http\Middleware;

use Closure;

class IdentifierMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (\Auth::guard('api')->check()) {
            $request->request->add(['user_id' => \Auth::guard('api')->user()->id]);
            $request->request->set('user_id' , \Auth::guard('api')->user()->id);
        }

        return $next($request);
    }
}
