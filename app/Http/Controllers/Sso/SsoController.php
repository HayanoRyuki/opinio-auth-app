<?php

namespace App\Http\Controllers\Sso;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\SsoCode;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class SsoController extends Controller
{
    public function start(Request $request)
    {
        $clientId    = $request->query('client_id');
        $redirectUri = $request->query('redirect_uri');
        $state       = $request->query('state');

        // 必須パラメータチェック
        if (! $clientId || ! $redirectUri) {
            abort(400, 'invalid_request');
        }

        // client 検証
        $client = Client::where('client_id', $clientId)
            ->where('status', 'active')
            ->first();

        if (! $client) {
            abort(400, 'invalid_client');
        }

        // redirect_uri 検証
        if (! in_array($redirectUri, (array) $client->allowed_redirect_uris, true)) {
            abort(400, 'invalid_redirect_uri');
        }

        // 認証済みユーザー取得
        $user = Auth::user();
        if (! $user) {
            abort(401, 'not_authenticated');
        }

        /**
         * client_user（clients リレーション）から membership を取得
         * ※ role / client 紐づきはここだけを見る
         */
        $clientUser = $user->clients()
            ->where('client_id', $client->client_id)
            ->first();

        if (! $clientUser) {
            abort(403, 'no_client_membership');
        }

        // SSO コード発行
        $code = \Illuminate\Support\Str::random(64);

        SsoCode::create([
            'id'         => (string) \Illuminate\Support\Str::uuid(),
            'code'       => $code,
            'user_id'    => $user->id,
            'company_id' => $user->company_id,
            'role'       => $clientUser->pivot->role ?? null,
            'client_id'  => $clientId,
            'expires_at' => now()->addMinutes(5),
        ]);

        // ATS にリダイレクト
        return redirect()->to(
            $redirectUri
            . '?code=' . urlencode($code)
            . '&state=' . urlencode($state ?? '')
        );
    }
}
