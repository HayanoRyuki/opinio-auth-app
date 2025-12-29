<?php

namespace App\Http\Controllers\Sso;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\JwtService;

class TokenController extends Controller
{
    public function issue(Request $request)
    {
        $code         = $request->input('code');
        $clientId     = $request->input('client_id');
        $clientSecret = $request->input('client_secret');

        // 仮クライアント検証（DBを使わない）
        if ($clientId !== 'ats' || $clientSecret !== 'secret') {
            return response()->json(['error' => 'invalid_client'], 401);
        }

        // 仮コード検証
        if ($code !== 'test') {
            return response()->json(['error' => 'invalid_code'], 400);
        }

        // JWT 発行
        $jwt = app(JwtService::class)->issue([
            'sub' => '1',
            'company_id' => '1',
            'role' => 'admin',
            'aud' => 'ats.opinio.co.jp',
        ]);

        return response()->json($jwt);
    }
}
