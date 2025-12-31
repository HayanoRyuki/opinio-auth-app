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
        $clientId    = $request->query('client_id');
        $redirectUri = $request->query('redirect_uri');
        $state       = $request->query('state');

        if (! $clientId || ! $redirectUri) {
            abort(400, 'invalid_request');
        }

        $client = Client::where('client_id', $clientId)
            ->where('status', 'active')
            ->first();

        if (! $client) {
            abort(400, 'invalid_client');
        }

        if (! in_array($redirectUri, (array) $client->allowed_redirect_uris, true)) {
            abort(400, 'invalid_redirect_uri');
        }

        $user = Auth::user();
        if (! $user) {
            abort(401, 'not_authenticated');
        }

        $clientUser = $user->clients()
            ->where('clients.client_id', $client->client_id)
            ->first();

        if (! $clientUser) {
            abort(403, 'no_client_membership');
        }

        $code = Str::random(64);

        SsoCode::create([
            'id'         => (string) Str::uuid(),
            'code'       => $code,
            'user_id'    => $user->id,
            'company_id' => $client->company_id, // ★ 修正点
            'role'       => $clientUser->pivot->role,
            'client_id'  => $clientId,
            'expires_at' => now()->addMinutes(5),
        ]);

        return redirect()->to(
            $redirectUri
            . '?code=' . urlencode($code)
            . '&state=' . urlencode($state ?? '')
        );
    }
}
