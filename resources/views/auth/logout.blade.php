<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ログアウト</title>
</head>
<body style="font-family:sans-serif; padding:40px;">

    <h1>ログアウトしますか？</h1>

    <form method="POST" action="/logout">
        @csrf
        <button type="submit"
            style="
                background:#e5533d;
                color:#fff;
                padding:10px 16px;
                border:none;
                border-radius:6px;
                font-size:16px;
                cursor:pointer;
            ">
            ログアウト
        </button>
    </form>

</body>
</html>
