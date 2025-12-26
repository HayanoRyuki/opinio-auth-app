<?php

namespace App\Http\Controllers\Sso;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Client;
use App\Models\SsoCode;
use App\Services\JwtService;

class TokenController extends Controller
{
    public function issue(Request $request)
    {
        $code          = $request->input('code');
        $clientId      = $request->input('client_id');
        $clientSecret  = $request->input('client_secret');

        if (! $code || ! $clientId || ! $clientSecret) {
            return response()->json(['error' => 'invalid_request'], 400);
        }

        // 1. client 検証
        $client = Client::where('client_id', $clientId)
            ->where('client_secret', $clientSecret)
            ->where('status', 'active')
            ->first();

        if (! $client) {
            return response()->json(['error' => 'invalid_client'], 401);
        }

        // 2. code 検証
        $ssoCode = SsoCode::where('code', $code)
            ->whereNull('used_at')
            ->first();

        if (! $ssoCode) {
            return response()->json(['error' => 'invalid_code'], 400);
        }

        if (Carbon::now()->greaterThan($ssoCode->expires_at)) {
            return response()->json(['error' => 'expired_code'], 400);
        }

        // 3. JWT 発行
        $jwt = app(JwtService::class)->issue([
            'sub' => (string) $ssoCode->user_id,
            'company_id' => (string) $ssoCode->company_id,
            'role' => $ssoCode->role,
            'aud' => $client->audience,
        ]);

        // 4. code を使用済みに
        $ssoCode->update([
            'used_at' => Carbon::now(),
        ]);

        return response()->json($jwt);
    }
}
