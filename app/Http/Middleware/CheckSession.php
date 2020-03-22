<?php

namespace App\Http\Middleware;

use Closure;
use Session;

class CheckSession
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
        if (Session::has('login')){
            return $next($request);
        }else{
            $response = [
                'msg' => 'Anda perlu login kembali!',
                'status_code' => '0006'
            ];

            return response()->json($response,404);
        }
    }
}
