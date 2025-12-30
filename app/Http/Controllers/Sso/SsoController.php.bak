<?php

namespace App\Http\Controllers\Sso;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Client;
use App\Models\Membership;
use App\Models\SsoCode;

class SsoController extends Controller
{
    public function start(Request $request)
    {
        $clientId     = $request->query('client_id');
        $redirectUri  = $request->query('redirect_uri');
        $state        = $request->query('state');

        if (! $clientId || ! $redirectUri) {
            abort(400, 'invalid_request');
        }

        // 1. client 検証
        $client = Client::where('client_id', $clientId)
            ->where('status', 'active')
            ->first();

        if (! $client) {
            abort(400, 'invalid_client');
        }

        // 2. redirect_uri 検証
        $allowedUris = json_decode($client->allowed_redirect_uris, true);

        if (! in_array($redirectUri, $allowedUris, true)) {
            abort(400, 'invalid_redirect_uri');
        }

        // 3. ログイン済ユーザー（いまは仮）
        // TODO: 本来は Auth::user()
        $userId = auth()->id() ?? \DB::table('users')->value('id');

        if (! $userId) {
            abort(401, 'unauthenticated');
        }

        // 4. membership を1件取得（仮：最初の1件）
        $membership = Membership::where('user_id', $userId)
            ->where('status', 'active')
            ->first();

        if (! $membership) {
            abort(403, 'no_membership');
        }

        // 5. SSO code 発行
        $code = Str::random(64);

        SsoCode::create([
            'id'         => (string) Str::uuid(),
            'code'       => $code,
            'user_id'    => $userId,
            'company_id' => $membership->company_id,
            'role'       => $membership->role,
            'client_id'  => $clientId,
            'expires_at' => Carbon::now()->addMinutes(5),
        ]);

        // 6. redirect
        $query = http_build_query([
            'code'  => $code,
            'state' => $state,
        ]);

        return redirect()->away($redirectUri . '?' . $query);
    }
}
