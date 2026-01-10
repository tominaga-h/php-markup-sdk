# 開発コマンド一覧

## Docker操作（Makefileを使用）

| コマンド | 説明 |
|---------|------|
| `make up` | Dockerコンテナを起動（バックグラウンド） |
| `make down` | Dockerコンテナを停止・削除 |
| `make shell` | コンテナ内でbashを実行 |
| `make install` | composer installを実行 |
| `make test` | PHPUnitテストを実行 |
| `make php` | PHP対話モード（REPL）を起動 |
| `make push` | テスト実行後にgit pushを実行 |

## composer scripts

| コマンド | 説明 |
|---------|------|
| `composer test` | PHPUnitテストを実行 |
| `composer upload-coverage` | カバレッジをアップロード |

## コンテナ名
`php-markup-sdk-php-1`

## 直接実行（コンテナ内）
- `./vendor/bin/phpunit` - テスト実行
- `php bin/console` - サンプル実行用エントリーポイント
