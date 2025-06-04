# COACHTECH 勤怠管理アプリ

## 概要

本アプリは、**出退勤打刻・勤怠一覧確認・修正申請・管理者による承認**などの機能を備えた、シンプルかつ実用的な勤怠管理システムです。
一般ユーザーと管理者の役割に応じた画面・機能の切り分けがなされており、Figma 準拠のデザインを元に**レスポンシブ対応**も行っています。

---

## 使用技術

| カテゴリ       | 使用技術               |
| -------------- | ---------------------- |
| フレームワーク | Laravel 8.83.8         |
| 言語           | PHP 7.4.9 / HTML / CSS     |
| フロントエンド | Blade テンプレート/ CSS |
| データベース   | MySQL 8.0.29           |
| 環境構築       | Docker / Docker Compose  |
| テスト         | PHPUnit                |
| バージョン管理 | Git / GitHub             |

---

## クローン・セットアップ手順

```bash

# 1.プロジェクトをクローン

git clone https://github.com/komataku02/coachtech-kintai.git

# 2.ディレクトリ移動

cd coachtech-kintai

# 3. .env ファイルの作成とキー生成

cp .env.example .env

php artisan key:generate

# 4. Docker コンテナビルド＆起動

docker-compose up -d --build

# 5. PHP コンテナに入る

docker compose exec php bash

# 6. 依存パッケージインストール

composer install

# 7. マイグレーションとシーディング（本番用）

php artisan migrate --seed
```
---

## テスト環境でのマイグレーション

### SQLiteファイルの作成（初回のみ）

テスト実行時に SQLite を使用するため、以下のコマンドで空ファイルを作成してください：
```bash
# PHP コンテナに入る

docker compose exec php bash

# Laravelプロジェクトのルート（/var/www）にいる状態で

touch database/database.sqlite

# テスト用 DB を初期化

php artisan migrate:fresh --env=testing

# テスト用シーディング

php artisan db:seed --env=testing

## テスト実行方法

php artisan test
```
---

## ログイン情報(管理者)

| ロール | メールアドレス    | パスワード  |
| ------ | ----------------- | ----------- |
| 管理者 | admin@example.com | password123 |


---

## アプリ URL

・開発環境：http://localhost

・phpMyAdmin：http://localhost:8080/

## 主な機能

### 一般ユーザー

-   出退勤打刻（出勤・退勤・休憩開始/終了）
-   勤怠一覧確認（月単位）
-   勤怠詳細閲覧・修正申請
-   修正申請一覧（承認待ち・承認済み）

### 管理者

- 日別勤怠一覧（全ユーザー）
- 個別勤怠詳細の閲覧・修正
- 勤怠修正申請一覧（承認処理）
- ユーザー一覧の確認
- スタッフ別勤怠一覧（CSV 出力含む）

---

## ER図

![ER図](https://github.com/komataku02/coachtech-kintai/blob/main/er-diagram.png?raw=true)