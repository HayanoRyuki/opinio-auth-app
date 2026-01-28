<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;

class LoginController extends Controller
{
    /**
     * ログイン画面表示（GET /login）
     */
    public function show()
    {
        return $this->renderWelcome();
    }

    /**
     * ログイン処理（POST /login）
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // ログイン後はトップ（welcome）へ
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'メールアドレスまたはパスワードが正しくありません。',
        ]);
    }

    /**
     * トップページ / ダッシュボード表示
     */
    public function welcome()
    {
        return $this->renderWelcome();
    }

    /**
     * Welcome/ダッシュボード画面を描画
     */
    private function renderWelcome()
    {
        $ssoLinks = [];

        if (Auth::check()) {
            $state = Str::random(32);
            $ssoLinks = [
                [
                    'name' => 'ATS アプリ',
                    'url' => url('/sso/start') . '?client_id=ats&redirect_uri=' . urlencode('https://ats.opinio.co.jp/sso/callback') . '&state=' . $state,
                    'color' => 'bg-blue-500 hover:bg-blue-600 text-white',
                ],
                [
                    'name' => '別アプリ',
                    'url' => 'https://other.opinio.co.jp',
                    'color' => 'bg-green-500 hover:bg-green-600 text-white',
                ],
            ];
        }

        return Inertia::render('Welcome', [
            'auth' => [
                'user' => Auth::user(),
            ],
            'ssoLinks' => $ssoLinks,
        ]);
    }
}
