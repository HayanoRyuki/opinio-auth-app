<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class VerifyJwt
{
    public function handle(Request $request, Closure $next)
    {
        $auth = $request->header('Authorization');

        if (! $auth || ! str_starts_with($auth, 'Bearer ')) {
            return response()->json(['error' => 'unauthorized'], 401);
        }

        $token = substr($auth, 7);

        try {
            $publicKey = file_get_contents(storage_path('oauth/public.key'));

            $decoded = JWT::decode($token, new Key($publicKey, 'RS256'));

            // aud は ATS 固定
            if ($decoded->aud !== 'ats.opinio.co.jp') {
                return response()->json(['error' => 'invalid_audience'], 401);
            }

            // 後続で使うため request に詰める
            $request->attributes->set('jwt', (array) $decoded);

        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'invalid_token',
            ], 401);
        }

        return $next($request);
    }
}
