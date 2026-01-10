# タスク完了時のチェックリスト

## 必須項目

### 1. テスト実行
```bash
make test
```
または
```bash
docker exec -it php-markup-sdk-php-1 composer test
```

### 2. コードスタイルの確認
- 日英両方のPHPDocコメントが記述されているか
- PHP 8.3の型宣言が使用されているか
- インターフェース/抽象クラスの命名規約に従っているか

### 3. ドキュメント更新
- 新しい機能を追加した場合は `docs/OVERVIEW.md` の更新を検討

## 推奨項目
- `bin/console` で動作確認（サンプル実行）
- 新しいToken/Node/Interpreterを追加した場合、適切なディレクトリに配置

## 注意事項
- 現在リンター/フォーマッターの設定はない
- テストファイルは `tests/` ディレクトリに配置（現在は`.gitkeep`のみ）
