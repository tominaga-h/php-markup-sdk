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

## テストの追加
新しいクラスを追加した場合は、対応するテストも追加する：
- テストファイルは `tests/` 配下に src/ の構造をミラーリングして配置
- テストクラス名は `{クラス名}Test.php` の形式
- 例: `src/MarkupSdk/Token/NewToken.php` → `tests/Token/NewTokenTest.php`

## 現在のテストカバレッジ
- Token: 全トークンクラスにテストあり
- Lexer: LexerTest.php
- Parser: Parser, ParserContext, TokenStream のテストあり
- Ast/Node: HeadingNode, DocumentNode, TextNode のテストあり
- Ast/Interpreter: HeadingInterpreter, TextInterpreter のテストあり
- Integration: MarkupParsingTest.php（統合テスト）

## 注意事項
- 現在リンター/フォーマッターの設定はない