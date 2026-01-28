# Opinio 共通認証（Auth）仕様書

## 最終更新: 2026-01-28

---

## 概要

Opinio共通認証は、複数のOpinioアプリケーション（ATS、Career、Interview等）に対して、シングルサインオン（SSO）を提供する認証基盤です。

**本番URL**: `https://auth.opinio.co.jp`

---

## 認証フロー

OAuth 2.0 Authorization Code Flowをベースとしたカスタム実装です。

```
┌──────────┐     ┌──────────┐     ┌──────────┐
│  User    │     │  Client  │     │   Auth   │
│ Browser  │     │  (ATS)   │     │  Server  │
└────┬─────┘     └────┬─────┘     └────┬─────┘
     │                │                │
     │ 1. アプリにアクセス              │
     │───────────────>│                │
     │                │                │
     │ 2. Auth へリダイレクト           │
     │<───────────────│                │
     │                │                │
     │ 3. /sso/start?client_id=ats&redirect_uri=...
     │────────────────────────────────>│
     │                │                │
     │ 4. (未ログインなら) ログイン画面  │
     │<────────────────────────────────│
     │                │                │
     │ 5. 認証成功 → code発行 → redirect_uri へ
     │<────────────────────────────────│
     │                │                │
     │ 6. ?code=xxx&state=yyy          │
     │───────────────>│                │
     │                │                │
     │                │ 7. POST /api/oauth/token
     │                │    (code + client_secret)
     │                │───────────────>│
     │                │                │
     │                │ 8. JWT access_token
     │                │<───────────────│
     │                │                │
     │ 9. セッション確立                │
     │<───────────────│                │
```

---

## エンドポイント

### 1. SSO開始（Authorization）

```
GET /sso/start
```

**パラメータ**:
| 名前 | 必須 | 説明 |
|------|------|------|
| client_id | ○ | クライアントスラッグ（例: `ats`） |
| redirect_uri | ○ | コールバックURL |
| state | △ | CSRF対策用ランダム文字列（推奨） |

**レスポンス**:
- 成功: `redirect_uri?code=xxx&state=yyy` へリダイレクト
- 失敗: HTTPエラー

**コード有効期限**: 5分

---

### 2. トークン発行

```
POST /api/oauth/token
Content-Type: application/x-www-form-urlencoded
```

**パラメータ**:
| 名前 | 必須 | 説明 |
|------|------|------|
| code | ○ | Authorization code |
| client_secret | ○ | クライアントシークレット |

**レスポンス**:
```json
{
  "access_token": "eyJhbGciOiJSUzI1NiIs...",
  "token_type": "Bearer",
  "expires_in": 3600
}
```

---

## JWTトークン仕様

### アルゴリズム
- **署名**: RS256（RSA + SHA-256）
- **鍵**: RSA 2048bit以上の秘密鍵/公開鍵ペア

### 鍵ファイルの場所
```
storage/oauth/private.key  # Auth Server（発行用）
storage/oauth/public.key   # Client Apps（検証用）
```

### ペイロード構造

```json
{
  "iss": "https://auth.opinio.co.jp",
  "aud": "ats.opinio.co.jp",
  "sub": "123",
  "company_id": "673d2a26-ee02-11f0-a187-0a89da368b75",
  "role": "admin",
  "iat": 1706450000,
  "exp": 1706453600,
  "jti": "550e8400-e29b-41d4-a716-446655440000"
}
```

| Claim | 説明 |
|-------|------|
| iss | 発行者（Auth Server URL） |
| aud | 対象アプリケーションのドメイン |
| sub | ユーザーID |
| company_id | 会社ID（UUID） |
| role | クライアント内での権限（admin/recruiter/interviewer/viewer） |
| iat | 発行日時（Unix timestamp） |
| exp | 有効期限（Unix timestamp）※発行から1時間 |
| jti | トークン固有ID（UUID） |

### トークン有効期限

- **access_token**: 1時間（3600秒）
- **Authorization code**: 5分（使い捨て）

---

## クライアントアプリでの検証

### 検証手順

1. `Authorization: Bearer <token>` ヘッダーからトークン取得
2. 公開鍵（public.key）でRS256署名を検証
3. `exp` で有効期限をチェック
4. `aud` が自分のドメインか確認
5. `iss` が `https://auth.opinio.co.jp` か確認

### Laravel実装例（VerifyJwtミドルウェア）

```php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$token = substr($request->header('Authorization'), 7);
$publicKey = file_get_contents(storage_path('oauth/public.key'));
$decoded = JWT::decode($token, new Key($publicKey, 'RS256'));

if ($decoded->aud !== 'ats.opinio.co.jp') {
    return response()->json(['error' => 'invalid_audience'], 401);
}

// $decoded->sub でユーザーID取得
// $decoded->role で権限チェック
```

---

## エラーコード一覧

### SSO開始時（/sso/start）

| HTTPコード | エラー | 原因 |
|------------|--------|------|
| 400 | invalid_request | client_id または redirect_uri が未指定 |
| 400 | invalid_client | 存在しないclient_id |
| 401 | not_authenticated | ユーザー未ログイン |
| 403 | no_client_membership | ユーザーがそのクライアントへのアクセス権なし |

### トークン発行時（/api/oauth/token）

| HTTPコード | エラー | 原因 |
|------------|--------|------|
| 400 | invalid_request | code または client_secret が未指定 |
| 400 | invalid_code | 存在しない、または使用済みのcode |
| 400 | code_expired | codeの有効期限切れ（5分超過） |
| 401 | invalid_client | client_secretが不正、またはクライアント無効 |

### JWT検証時（クライアントアプリ側）

| エラー | 原因 |
|--------|------|
| unauthorized | Authorizationヘッダーなし、またはBearer形式でない |
| invalid_token | 署名検証失敗、有効期限切れ、不正なフォーマット |
| invalid_audience | audが想定と異なる |

---

## 障害時の影響

### Auth Server（EC2）がダウンした場合

#### 影響を受けるもの

1. **新規ログイン**: 不可
   - ユーザーはどのアプリにもログインできなくなる
   - `/sso/start` が応答しない

2. **新規トークン発行**: 不可
   - `/api/oauth/token` が応答しない
   - Authorization codeを持っていてもトークン取得不可

3. **セッション切れ後の再認証**: 不可
   - 既存セッションが切れたユーザーは再ログイン不可

#### 影響を受けないもの

1. **既存セッション**: 継続可能
   - クライアントアプリ側でセッション管理している場合
   - セッションが有効な間は操作可能

2. **JWT検証**: 継続可能
   - 公開鍵はクライアントアプリがローカル保持
   - Auth Serverに問い合わせ不要
   - 有効期限内のトークンは引き続き使用可能

#### 復旧優先度

```
[最高] Auth EC2の復旧
  ↓
[高]  新規ログインの復旧確認
  ↓
[中]  トークン発行の動作確認
  ↓
[低]  各クライアントアプリの疎通確認
```

### RDS（MySQL）がダウンした場合

- SSO code発行/検証不可
- ユーザー認証不可
- 既発行のJWTは引き続き有効（DBアクセス不要）

### 秘密鍵が漏洩した場合

1. 即座に新しい鍵ペアを生成
2. 全クライアントアプリの公開鍵を更新
3. 既存トークンは有効期限まで有効（必要なら手動無効化）

---

## セキュリティ考慮事項

### 必須

- [ ] HTTPS通信の強制
- [ ] client_secretの安全な保管（環境変数）
- [ ] 公開鍵のみをクライアントアプリに配布
- [ ] stateパラメータによるCSRF対策

### 推奨

- [ ] トークンの短い有効期限（現在1時間）
- [ ] IPアドレス制限（必要に応じて）
- [ ] Rate Limiting（ブルートフォース対策）
- [ ] 監査ログの記録

---

## クライアント登録情報

### clients テーブル

| カラム | 説明 |
|--------|------|
| id | UUID |
| slug | クライアント識別子（例: ats） |
| name | 表示名 |
| client_secret | シークレットキー |
| redirect_uri | 許可されたコールバックURL |
| status | active / suspended |

### client_user テーブル（権限管理）

| カラム | 説明 |
|--------|------|
| user_id | ユーザーID |
| client_id | クライアントID |
| role | クライアント内権限 |

---

## 今後の拡張予定

- [ ] Refresh Token対応
- [ ] トークン無効化（Revocation）エンドポイント
- [ ] PKCE対応（モバイルアプリ向け）
- [ ] OpenID Connect準拠
- [ ] 多要素認証（MFA）

---

## 関連ファイル

```
app/
├── Http/Controllers/Sso/
│   ├── SsoController.php      # /sso/start
│   └── TokenController.php    # /api/oauth/token
├── Services/
│   └── JwtService.php         # JWT発行
├── Http/Middleware/
│   └── VerifyJwt.php          # JWT検証（サンプル）
└── Models/
    ├── Client.php
    ├── SsoCode.php
    └── User.php

storage/oauth/
├── private.key                # JWT署名用秘密鍵
└── public.key                 # JWT検証用公開鍵
```
