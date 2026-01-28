# Opinio Auth App - 引き継ぎメモ

## 最終更新: 2026-01-28

---

## 今回の作業内容

### Vue/Inertia移行（完了）
- BladeテンプレートからVue 3 + Inertia.jsに全面移行
- 以下のVueコンポーネントを作成:
  - `resources/js/Pages/Welcome.vue` - トップページ/ログイン
  - `resources/js/Pages/Auth/Logout.vue` - ログアウト確認
  - `resources/js/Pages/Profile/Edit.vue` - プロフィール編集
  - `resources/js/Layouts/AuthLayout.vue` - 共通レイアウト

### マイページ編集機能（完了）
- `/profile` - プロフィール編集画面
- 名前・メールアドレス変更機能
- パスワード変更機能
- `ProfileController.php` を新規作成

### 本番デプロイ（完了）
- t3.microはメモリ不足でnpm run buildが失敗
- **対策**: ローカルでビルドしてscpでアップロード
- Node.js 12→20にアップグレード済み

---

## 現在の状態

### 本番環境
- https://auth.opinio.co.jp - 正常稼働
- Vue/Inertia移行済み
- マイページ編集機能稼働中

### 未コミットの変更
```bash
cd ~/opinio/apps/auth-app
git add CLAUDE.md HANDOVER.md
git commit -m "docs: CLAUDE.md, HANDOVER.md追加"
git push origin main
```

---

## 次回やること

### メンバー管理機能（管理者向け）
- 同じ会社ドメイン内で管理者ができること:
  - メンバーシップ（アカウント）の作成
  - メンバーシップの削除
  - 権限の変更
- 対象テーブル: `users`, `memberships`, `client_user`など

### ATS側との連携
- ATS側のMyPage/Index.vueの`authProfileUrl`をAuth側に向ける
- `https://auth.opinio.co.jp/profile` にリンク設定

### 動作確認項目
- [ ] ログイン/ログアウト
- [ ] プロフィール編集（名前・メール）
- [ ] パスワード変更
- [ ] ATS側からプロフィール編集ページへの遷移

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
