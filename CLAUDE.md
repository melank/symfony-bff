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
- 型宣言を必ず使用
- **PHP 8.5の最新言語仕様を積極的に使用すること**

## PHP 8.5 言語仕様（必須）

このプロジェクトではPHP 8.5の最新機能を積極的に活用します。

### 必須で使用する機能

```php
// 1. Constructor property promotion + readonly
class UserService
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $apiEndpoint,
    ) {}
}

// 2. Property hooks（ゲッター/セッターの代替）
class User
{
    public string $fullName {
        get => $this->firstName . ' ' . $this->lastName;
    }

    public string $email {
        set => strtolower($value);
    }
}

// 3. Asymmetric visibility（非対称可視性）
class Entity
{
    public private(set) string $id;      // 外部から読み取り可、書き込み不可
    public protected(set) int $count;    // 継承クラスからのみ書き込み可
}

// 4. Pipe operator（パイプ演算子）
$result = $input
    |> trim(...)
    |> strtolower(...)
    |> ucfirst(...);

// 5. Named arguments（名前付き引数）
$response = $this->httpClient->request(
    method: 'POST',
    url: $endpoint,
    options: ['json' => $data],
);

// 6. Match expression（match式）
$statusText = match($statusCode) {
    200 => 'OK',
    404 => 'Not Found',
    500 => 'Internal Server Error',
    default => 'Unknown',
};

// 7. Null safe operator（null安全演算子）
$userName = $user?->getProfile()?->getName();

// 8. First-class callable syntax
$callback = $this->someMethod(...);
array_map($transformer->transform(...), $items);
```

### 推奨パターン

```php
// DTOはreadonlyクラスで定義
readonly class UserDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $email = null,
    ) {}
}

// Enumを積極的に使用
enum Status: string
{
    case PENDING = 'pending';
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
}

// Union typesで柔軟な型定義
function process(string|array $input): array|false
{
    // ...
}

// Intersection typesで厳密な型制約
function handle(Countable&Iterator $collection): void
{
    // ...
}
```

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
