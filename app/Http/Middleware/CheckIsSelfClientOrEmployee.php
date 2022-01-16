<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckIsSelfClientOrEmployee
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

        if(isset(Auth::user()->client->id))
        {
            if(Auth::user()->client->id == $request->route()->parameter('idClient'))
                return $next($request);
        }

        if(Auth::user()->employee)
            return $next($request);


        return response()->json(['error' => 'Unauthorized - Recurso no le pertenece'], 403);

    }
}
