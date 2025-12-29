<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Firebase\JWT\JWT;

class DevTokenController extends Controller
{
    public function issue(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'invalid_credentials'], 401);
        }

        $privateKey = file_get_contents(storage_path('oauth/private.key'));

        $payload = [
            'iss' => config('app.url'),
            'sub' => (string) $user->id,
            'aud' => 'ats.opinio.co.jp',
            'iat' => time(),
            'exp' => time() + 3600,
            'company_id' => (string) ($user->company_id ?? 1),
            'role' => $user->role ?? 'admin',
        ];

        $jwt = JWT::encode($payload, $privateKey, 'RS256');

        return response()->json([
            'access_token' => $jwt,
            'token_type'   => 'Bearer',
            'expires_in'   => 3600,
        ]);
    }
}
