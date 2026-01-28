# Opinio Auth App - 開発ガイド

## プロジェクト概要

- **アプリ名**: auth-app（共通認証サーバー）
- **ドメイン**: auth.opinio.co.jp
- **フレームワーク**: Laravel 12 + Vue 3 + Inertia.js
- **役割**: SSO認証基盤（JWT発行）、ユーザー管理、プロフィール編集

---

## 編集・作業ルール（厳守）

### ❌ 禁止
- プレースホルダ使用（IP / パス / パスワード含む）
- パスワード伏字・省略
- ファイル全置換（事前確認なし）
- 未確認ファイルへの修正提案
- vim / vi / エディタ操作
- 推測・「たぶん」「おそらく」を前提に進めること

### ✅ 許可
- sed
- cat <<'EOF'
- 読み取り系コマンド（sed -n, grep, tail）
- 現状確認 → 事実確定 → 次の1手

### 進め方の原則
1. 現状確認（コード or ログを必ず見る）
2. 事実を確定
3. 引き継ぎメモに反映
4. 次の1コマンドだけ出す

※ 修正は必ず「どのアプリ / どのファイル / どの行」かを先に確定

---

## ローカル開発

### 起動コマンド
```bash
cd ~/opinio/apps/auth-app
composer dev
```

### 開発URL
- http://auth.opinio.co.jp (hostsで127.0.0.1に向ける)
- http://localhost:8000

### ビルド
```bash
npm run build
```

---

## AWS 本番環境

### auth-app サーバー
| 項目 | 値 |
|------|-----|
| OS | Ubuntu 22.04 |
| User | ubuntu |
| Public IP | 3.114.99.99 |
| 秘密鍵 | ~/.ssh/opinio-2026.pem |
| アプリパス | /var/www/auth-app |

### 直接接続
```bash
ssh -i ~/.ssh/opinio-2026.pem ubuntu@3.114.99.99
```

### 接続後の作業
```bash
cd /var/www/auth-app

# よく使うコマンド
php artisan route:list
php artisan optimize:clear
tail -n 100 storage/logs/laravel.log
```

---

## データベース（本番RDS）

```bash
mysql -u admin -p -h opinio-auth-prod-db.cbgicmw8gpvm.ap-northeast-1.rds.amazonaws.com
```

| 項目 | 値 |
|------|-----|
| Host | opinio-auth-prod-db.cbgicmw8gpvm.ap-northeast-1.rds.amazonaws.com |
| Database | opinio_auth_prod |
| User | admin |
| Password | AUTHProd!2026rds |

---

## 踏み台サーバー（bastion）

| 項目 | 値 |
|------|-----|
| OS | Amazon Linux 2023 |
| User | ec2-user |
| Public IP | 52.195.228.179 |
| 秘密鍵 | ~/Hayano.pem |

### 接続
```bash
ssh -i ~/Hayano.pem ec2-user@52.195.228.179
```

---

## 関連サーバー

### ATS（業務アプリ）
| 項目 | 値 |
|------|-----|
| Public IP | 13.114.111.187 |
| Private IP | 10.0.4.1 |
| アプリパス | /var/www/ats-app |

```bash
# 直接接続
ssh -i ~/.ssh/opinio-2026.pem ubuntu@13.114.111.187

# bastion経由
ssh -i ~/opinio-2026.pem ubuntu@10.0.4.1
```

---

## Git運用ルール

### ブランチ
| ブランチ名 | 用途 |
|-----------|------|
| main | 本番と常に同期 |
| fix/* | 一時修正・開発用 |

### デプロイ手順

#### ローカル
```bash
git add .
git commit -m "update: <内容> (YYYY-MM-DD)"
git push origin main
```

#### 本番
```bash
cd /var/www/auth-app
git fetch origin
git reset --hard origin/main
composer install
npm install
npm run build
php artisan optimize:clear
```

---

## 技術スタック

### バックエンド
- Laravel 12
- Inertia.js 2.0
- JWT認証（firebase/php-jwt）
- Ziggy（ルート生成）

### フロントエンド
- Vue 3.5
- Vite 7
- Tailwind CSS 4

### 主要ファイル構成
```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   │   ├── LoginController.php
│   │   │   └── LogoutController.php
│   │   ├── ProfileController.php
│   │   └── Sso/
│   │       └── SsoController.php
│   └── Middleware/
│       └── HandleInertiaRequests.php
├── Models/
│   ├── User.php
│   ├── Client.php
│   └── SsoCode.php
resources/
├── js/
│   ├── app.js
│   ├── Layouts/
│   │   └── AuthLayout.vue
│   └── Pages/
│       ├── Welcome.vue
│       ├── Auth/
│       │   └── Logout.vue
│       └── Profile/
│           └── Edit.vue
└── views/
    └── app.blade.php
```

---

## API エンドポイント

### 認証
| Method | Path | 説明 |
|--------|------|------|
| GET | /login | ログイン画面 |
| POST | /login | ログイン処理 |
| GET | /logout | ログアウト確認 |
| POST | /logout | ログアウト実行 |

### プロフィール
| Method | Path | 説明 |
|--------|------|------|
| GET | /profile | 編集画面 |
| PUT | /profile | 基本情報更新 |
| PUT | /profile/password | パスワード変更 |

### SSO
| Method | Path | 説明 |
|--------|------|------|
| GET | /sso/start | SSO認証開始 |
| POST | /api/oauth/token | JWT発行 |

---

## トラブルシューティング

### SSH接続エラー
```bash
# known_hostsクリア
ssh-keygen -R 3.114.99.99

# 詳細ログ
ssh -v -i ~/.ssh/opinio-2026.pem ubuntu@3.114.99.99
```

### Laravelキャッシュクリア
```bash
php artisan optimize:clear
php artisan view:clear
php artisan config:clear
```

### Viteビルドエラー
```bash
rm -rf node_modules
npm install
npm run build
```

---

## 最短フロー

```
ローカル修正 → git push origin main
                ↓
        本番 git fetch + reset --hard origin/main
                ↓
        composer install → npm install → npm run build
                ↓
        php artisan optimize:clear
```
