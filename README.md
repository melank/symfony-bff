# Symfony BFF

Symfony 8.0をベースとしたBFF（Backend for Frontend）アプリケーション。GraphQLバックエンドAPIへのプロキシとして機能します。

## 必要要件

- Docker & Docker Compose
- （ローカル開発の場合）PHP 8.5+、Composer

## セットアップ

```bash
# リポジトリをクローン
git clone <repository-url>
cd symfony-bff

# 環境変数を設定
cp .env.dev .env.local
# .env.local を編集してGRAPHQL_ENDPOINTを設定

# Dockerコンテナを起動
docker compose up -d
```

## アクセス

- **アプリケーション**: http://localhost:8080
- **ヘルスチェック**: http://localhost:8080/health

## API エンドポイント

| メソッド | パス | 説明 |
|---------|------|------|
| GET | `/health` | ヘルスチェック |
| POST | `/api/graphql` | GraphQLプロキシ |

### GraphQLプロキシの使用例

```bash
curl -X POST http://localhost:8080/api/graphql \
  -H "Content-Type: application/json" \
  -d '{"query": "{ users { id name } }"}'
```

## 開発

```bash
# コンテナ起動
docker compose up -d

# ログ確認
docker compose logs -f

# PHPコンテナに入る
docker compose exec php bash

# コンテナ停止
docker compose down
```

## ディレクトリ構成

```
.
├── config/             # Symfony設定
├── docker/             # Docker関連ファイル
│   ├── nginx/          # Nginx設定
│   └── php/            # PHP Dockerfile
├── public/             # 公開ディレクトリ
├── src/
│   ├── Controller/     # コントローラー
│   └── Service/        # サービスクラス
├── var/                # キャッシュ・ログ（gitignore）
└── vendor/             # 依存パッケージ（gitignore）
```

## 環境変数

| 変数名 | 説明 | デフォルト |
|--------|------|-----------|
| `APP_ENV` | 環境（dev/prod） | `dev` |
| `APP_SECRET` | アプリケーションシークレット | - |
| `GRAPHQL_ENDPOINT` | バックエンドGraphQL URL | `http://localhost:4000/graphql` |

## 技術スタック

- PHP 8.5.1
- Symfony 8.0
- Nginx (Alpine)
- Docker Compose
