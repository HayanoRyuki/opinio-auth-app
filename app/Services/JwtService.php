<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Str;
use Carbon\Carbon;

class JwtService
{
    private string $privateKey;
    private string $issuer;

    public function __construct()
    {
        $this->privateKey = file_get_contents(storage_path('oauth/private.key'));
        $this->issuer = config('app.url');
    }

    public function issue(array $claims): array
    {
        $now = Carbon::now()->timestamp;

        $payload = array_merge([
            'iss' => $this->issuer,
            'iat' => $now,
            'exp' => $now + (60 * 60), // 1 hour
            'jti' => (string) Str::uuid(),
        ], $claims);

        $token = JWT::encode($payload, $this->privateKey, 'RS256');

        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => 3600,
        ];
    }
}
