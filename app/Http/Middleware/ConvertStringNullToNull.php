<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ConvertStringNullToNull
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $headersToCheck = ['X-Household-Id', 'X-User-Id', 'X-SystemAdmin-Id'];
        foreach ($headersToCheck as $header) {
            $val = $request->header($header);
            if ($val === 'null' || $val === 'undefined') {
                $request->headers->remove($header);
            }
        }

        return $next($request);
    }
}
