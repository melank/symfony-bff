# CLAUDE.md

このファイルはClaude Codeがこのリポジトリで作業する際のガイダンスを提供します。

## プロジェクト概要

Symfony 8.0ベースのBFF（Backend for Frontend）アプリケーション。フロントエンドからのリクエストを受け、GraphQLバックエンドAPIへプロキシします。

## 技術スタック

- **PHP**: 8.5.1
- **フレームワーク**: Symfony 8.0
- **コンテナ**: Docker (PHP-FPM + Nginx)
- **バックエンド連携**: GraphQL (HTTP Client)

## 開発コマンド

```bash
# Docker環境
docker-compose up -d          # 起動
docker-compose down           # 停止
docker-compose logs -f        # ログ確認
docker-compose exec php sh    # PHPコンテナに入る

# Symfonyコマンド（コンテナ内）
php bin/console cache:clear   # キャッシュクリア
php bin/console debug:router  # ルーティング確認

# Composer（コンテナ内）
composer install              # 依存インストール
composer require <package>    # パッケージ追加
```

## アーキテクチャ

```
[Frontend] → [BFF (Symfony)] → [GraphQL Backend]
                  ↓
            - 認証/認可
            - リクエスト変換
            - レスポンス整形
            - キャッシュ
```

## 主要ファイル

- `src/Service/GraphQLClient.php` - GraphQLクライアントサービス
- `src/Controller/BffController.php` - BFFプロキシコントローラー
- `src/Controller/HealthController.php` - ヘルスチェック
- `config/services.yaml` - サービス定義
- `docker-compose.yml` - Docker構成

## コーディング規約

- PSR-12準拠
- Symfonyベストプラクティスに従う
- コントローラーは薄く、ロジックはサービスに
- 型宣言を必ず使用（PHP 8.5の機能を活用）

## 環境変数

開発時は `.env.local` を作成して設定を上書き：

```bash
GRAPHQL_ENDPOINT=http://your-graphql-server/graphql
```

## テスト

```bash
# PHPUnit（コンテナ内）
php bin/phpunit
```

## 注意事項

- `var/` と `vendor/` はgitignore対象
- `.env` はgitignore対象、`.env.dev` をテンプレートとして使用
- 本番シークレットは `config/secrets/` で管理
