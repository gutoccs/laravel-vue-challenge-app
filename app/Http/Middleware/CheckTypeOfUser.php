<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckTypeOfUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $type)
    {
        if($type == "client") {
            if(Auth::user()->client) return $next($request);
        }

        if($type == "employee") {
            if(Auth::user()->employee) return $next($request);
        }

        return response()->json(['error' => 'Unauthorized - Tipo de Usuario no válido para esta solicitud'], 403);
    }
}
