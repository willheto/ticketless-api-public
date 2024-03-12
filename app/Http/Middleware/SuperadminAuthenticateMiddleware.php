<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\User;

class SuperadminAuthenticateMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        try {
            $token = $request->header('Authorization');

            if (!$token) {
                throw new Exception('Unauthorized', 401);
            }

            // Extract and verify the JWT token
            $jwt = str_replace('Bearer ', '', $token);

            $decoded = JWT::decode($jwt, new Key(env('JWT_SECRET'), 'HS256'));
            if ($decoded->userID === false) {
                throw new Exception('Unauthorized', 401);
            }

            $user = User::where('userID', $decoded->userID)->first();

            if (!$user) {
                throw new Exception('Unauthorized', 401);
            }

            if ($user->userType !== 'superadmin') {
                throw new Exception('Unauthorized', 401);
            }

            $request->merge(['userID' => $decoded->userID]);
            return $next($request);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }
}
