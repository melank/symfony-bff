# AGENTS.md

AIエージェントがこのリポジトリで作業する際のガイドラインです。

## プロジェクトコンテキスト

このプロジェクトはSymfony 8.0を使用したBFF（Backend for Frontend）です。フロントエンドアプリケーションとGraphQLバックエンドの間に位置し、以下の責務を担います：

- GraphQLクエリのプロキシ
- 認証・認可の処理
- レスポンスの変換・整形
- キャッシュ戦略の実装

## 作業時の原則

### コード変更時

1. **Symfonyの規約に従う**
   - サービスはDI（依存性注入）で管理
   - コントローラーはルーティングとレスポンス生成のみ
   - ビジネスロジックはServiceクラスに配置

2. **PHP 8.5の最新言語仕様を積極的に使用（必須）**

   以下の機能は必ず使用すること：

   | 機能 | 用途 | 例 |
   |------|------|-----|
   | Constructor property promotion | コンストラクタ引数 | `public function __construct(private readonly string $name)` |
   | Property hooks | ゲッター/セッター代替 | `public string $name { get => ...; set => ...; }` |
   | Asymmetric visibility | 読み取り専用プロパティ | `public private(set) string $id;` |
   | Pipe operator | データ変換チェーン | `$x \|> trim(...) \|> strtolower(...)` |
   | Named arguments | 可読性向上 | `request(method: 'POST', url: $url)` |
   | Match expression | switch文の代替 | `match($code) { 200 => 'OK', ... }` |
   | Null safe operator | null安全なチェーン | `$user?->profile?->name` |
   | First-class callable | コールバック | `array_map($obj->method(...), $items)` |
   | readonly class | 不変DTO | `readonly class UserDTO { ... }` |
   | Enum | 列挙型 | `enum Status: string { case ACTIVE = 'active'; }` |
   | Union/Intersection types | 複合型 | `string\|array`, `Countable&Iterator` |

3. **型安全性を重視**
   - すべてのメソッドに戻り値型を宣言
   - パラメータには型宣言を必須とする
   - nullableは明示的に `?Type` で表現

### ファイル配置

```
src/
├── Controller/     # HTTPリクエスト/レスポンス処理
├── Service/        # ビジネスロジック
├── DTO/            # データ転送オブジェクト（必要に応じて作成）
├── Exception/      # カスタム例外（必要に応じて作成）
└── EventListener/  # イベントリスナー（必要に応じて作成）
```

### 新しいエンドポイント追加時

1. `src/Controller/` に新しいコントローラーを作成
2. ルーティングはPHP Attributeで定義
3. 複雑なロジックは `src/Service/` にサービスクラスを作成
4. 必要に応じて `config/services.yaml` にサービス定義を追加

### GraphQL連携

`GraphQLClient` サービスを使用：

```php
public function __construct(
    private readonly GraphQLClient $graphQLClient,
) {}

public function someAction(): JsonResponse
{
    $result = $this->graphQLClient->query($query, $variables);
    return $this->json($result);
}
```

## テスト

- 新機能追加時はユニットテストを作成
- `tests/` ディレクトリに配置
- PHPUnitを使用

## Docker環境

- 開発はDocker環境で行う
- `docker compose exec php bash` でPHPコンテナに入る
- Composerコマンドはコンテナ内で実行

## 禁止事項

- `.env` ファイルに機密情報を直接記載しない
- `var/` や `vendor/` をコミットしない
- コントローラーに直接SQLやビジネスロジックを書かない
- グローバル状態に依存するコードを書かない

## 推奨事項

- 変更前に `docker compose ps` でコンテナ状態を確認
- 大きな変更前に `git status` で状態を確認
- キャッシュ問題が疑われる場合は `php bin/console cache:clear`
