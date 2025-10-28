<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(auth()->user()->tokenCan('role:user')) {

            return $next($request);
        } else {

            return response()->json([
                'message' => 'unauthorized' ,
                'success' => false,
                'status' => 401
            ] , 200);
        }
    }
}
