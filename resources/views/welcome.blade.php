<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Opinio Auth</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">

<div class="w-full max-w-lg p-8 bg-white shadow-lg rounded-lg border border-gray-200">

    <h1 class="text-3xl font-bold text-center text-gray-800 mb-6">
        Opinio 共通認証
    </h1>

    @if (Auth::check())

        <p class="text-center text-gray-700 mb-6">
            ログイン済み:
            <span class="font-medium">{{ Auth::user()->email }}</span>
        </p>

        @php
            $state = \Illuminate\Support\Str::random(32);
        @endphp

        <div class="space-y-4">
            <a
              href="{{ url('/sso/start') }}?client_id=ats&redirect_uri={{ urlencode('https://ats.opinio.co.jp') }}&state={{ $state }}"
              class="block text-center bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 rounded transition"
            >
              ATS アプリ
            </a>

            <a
              href="https://other.opinio.co.jp"
              class="block text-center bg-green-500 hover:bg-green-600 text-white font-semibold py-2 rounded transition"
            >
              別アプリ
            </a>
        </div>

        <form method="POST" action="{{ route('logout') }}" class="mt-6 text-center">
            @csrf
            <button type="submit" class="text-sm text-gray-500 hover:underline">
                ログアウト
            </button>
        </form>

    @else

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-gray-700 font-medium mb-1">
                    メールアドレス
                </label>
                <input
                  type="email"
                  name="email"
                  id="email"
                  required
                  class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
                >
            </div>

            <div>
                <label for="password" class="block text-gray-700 font-medium mb-1">
                    パスワード
                </label>
                <input
                  type="password"
                  name="password"
                  id="password"
                  required
                  class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
                >
            </div>

            <button
              type="submit"
              class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 rounded transition"
            >
              ログイン
            </button>
        </form>

    @endif

    <p class="mt-6 text-center text-gray-400 text-sm">
        © 2025 Opinio Inc. All Rights Reserved.
    </p>

</div>

</body>
</html>
