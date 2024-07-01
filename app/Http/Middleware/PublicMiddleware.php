<?php

namespace App\Http\Middleware;

use Closure;

class PublicMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->header('Authorization')) {
            if (auth()->guard('api')->check()) {
                $request->request->add(['user_id' => auth()->guard('api')->user()->id]);
            }
        }
        return $next($request);
    }
}
