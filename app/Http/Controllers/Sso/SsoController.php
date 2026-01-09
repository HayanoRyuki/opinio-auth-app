<?php

namespace App\Http\Controllers\Sso;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\SsoCode;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SsoController extends Controller
{
    public function start(Request $request)
    {
        $clientSlug  = $request->query('client_id'); // ats
        $redirectUri = $request->query('redirect_uri');
        $state       = $request->query('state');

        if (! $clientSlug || ! $redirectUri) {
            abort(400, 'invalid_request');
        }

        // client = slug で取得
        $client = Client::where('slug', $clientSlug)->first();
        if (! $client) {
            abort(400, 'invalid_client');
        }

        // ログイン確認
        $user = Auth::user();
        if (! $user) {
            abort(401, 'not_authenticated');
        }

        // client 所属確認（pivot）
        $clientUser = $user->clients()
            ->where('clients.id', $client->id)
            ->first();

        if (! $clientUser) {
            abort(403, 'no_client_membership');
        }

        // SSO code 発行
        $code = Str::random(64);

        SsoCode::create([
            'id'         => (string) Str::uuid(),
            'code'       => $code,
            'user_id'    => $user->id,
            'role'       => $clientUser->pivot->role,
            'client_id'  => $client->id,
            'expires_at' => now()->addMinutes(5),
        ]);

        return redirect()->to(
            $redirectUri
            . '?code=' . urlencode($code)
            . '&state=' . urlencode($state ?? '')
        );
    }
}
