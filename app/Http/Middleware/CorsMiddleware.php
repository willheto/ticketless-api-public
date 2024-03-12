<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CorsMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        // Intercepts OPTIONS requests
        if ($request->isMethod('OPTIONS')) {
            $allowedOrigin = $request->header('Origin');

            if(!$allowedOrigin) {
                return $next($request);
            }

            return response()
                ->json([], 204)
                ->header('Access-Control-Allow-Origin', $allowedOrigin)
                ->header('Access-Control-Allow-Methods', 'GET, POST, PATCH, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Authorization')
                ->header('Access-Control-Allow-Credentials', 'true');
        }

        // For all other requests, add the CORS headers
        $allowedOrigin = $request->header('Origin');

        return $next($request)
            ->header('Access-Control-Allow-Origin', $allowedOrigin)
            ->header('Access-Control-Allow-Methods', 'GET, POST, PATCH, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Authorization')
            ->header('Access-Control-Allow-Credentials', 'true');
    }
}
