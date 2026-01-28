# Opinio Auth App - 引き継ぎメモ

## 最終更新: 2026-01-28

---

## 今回の作業内容

### メンバー管理機能（実装中）
- 管理者（adminロール）が同じ会社のメンバーを管理できる機能
- `/admin/members` に専用管理画面を新規作成

#### 作成したファイル
- `app/Http/Middleware/AdminMiddleware.php` - admin権限チェック
- `app/Http/Controllers/Admin/MemberController.php` - CRUD操作
- `resources/js/Pages/Admin/Members/Index.vue` - メンバー一覧
- `resources/js/Pages/Admin/Members/Create.vue` - 新規メンバー作成
- `resources/js/Pages/Admin/Members/Edit.vue` - 権限編集

#### 修正したファイル
- `bootstrap/app.php` - AdminMiddlewareをalias登録
- `routes/web.php` - /admin/membersルート追加
- `app/Models/User.php` - memberships()リレーション、isAdmin()メソッド追加
- `app/Models/Membership.php` - user()リレーション追加
- `app/Http/Middleware/HandleInertiaRequests.php` - isAdminフラグをshare
- `resources/js/Layouts/AuthLayout.vue` - 管理者向けメニューリンク追加

#### 機能詳細
- **一覧表示**: `/admin/members` - 同じ会社のメンバー一覧
- **新規作成**: `/admin/members/create` - name, email, password, roleを入力
- **権限編集**: `/admin/members/{id}/edit` - roleを変更
- **削除**: membershipのstatusを'revoked'に変更（論理削除）

#### 権限（role）の種類
- admin: 管理者（メンバー管理可能）
- recruiter: 採用担当
- interviewer: 面接官
- viewer: 閲覧者

---

## 未コミットの変更

```bash
cd ~/opinio/apps/auth-app
git add .
git commit -m "feat: 管理者メンバー管理機能"
git push origin main
```

---

## 動作確認前の準備

### 1. membershipsにadminレコードが必要
テストユーザーに対してmembershipsテーブルにadminロールのレコードを作成:

```sql
-- 現在のユーザー情報を確認
SELECT id, name, email, company_id FROM users;

-- membershipsにadminレコードを追加（例）
INSERT INTO memberships (id, user_id, company_id, role, status, created_at, updated_at)
VALUES (UUID(), <user_id>, '<company_id>', 'admin', 'active', NOW(), NOW());
```

### 2. ローカルビルド
```bash
cd ~/opinio/apps/auth-app
npm run build
```

### 3. 動作確認
- `/admin/members` にアクセスしてメンバー一覧が表示されるか
- 新規メンバー作成ができるか
- 権限変更ができるか
- メンバー削除ができるか
- admin以外のユーザーは403エラーになるか

---

## 次回やること

### ATS側との連携
- ATS側のMyPage/Index.vueの`authProfileUrl`をAuth側に向ける
- `https://auth.opinio.co.jp/profile` にリンク設定

### 追加機能（検討）
- ownerロールの実装（adminより上位の権限）
- メンバー招待機能（既存ユーザーを会社に追加）
- パスワードリセット機能（管理者から）

---

## 本番デプロイ手順（メモリ不足対策版）

```bash
# ローカル
cd ~/opinio/apps/auth-app
npm run build
git add . && git commit -m "update: <内容>" && git push origin main

# ビルドファイルをアップロード
scp -i ~/.ssh/opinio-2026.pem -r public/build ubuntu@3.114.99.99:/var/www/auth-app/public/

# 本番
ssh -i ~/.ssh/opinio-2026.pem ubuntu@3.114.99.99
cd /var/www/auth-app
git fetch origin && git reset --hard origin/main
composer install --no-dev
php artisan optimize:clear
```

---

## 注意事項

- **本番サーバー（t3.micro）ではnpm run buildを実行しない**
  - メモリ1GBでViteビルドがフリーズする
  - 必ずローカルでビルドしてアップロード
