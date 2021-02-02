<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EnsurePatientIsVerified
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
        if ($request->user('patient')->verified_at) {
            return $next($request);
        } else {
            abort(Response::HTTP_FORBIDDEN, 'you are not verified yet');
        }
    }
}
