# コードスタイルと規約

## 名前空間
- ルート名前空間: `Hytmng\MarkupSdk`
- テスト名前空間: `Tests`

## オートロード（PSR-4）
- `Hytmng\MarkupSdk\` → `src/MarkupSdk/`
- `Tests\` → `tests/`

## 命名規約
- インターフェース: `Interface`サフィックスを使用（例: `TokenInterface`, `NodeInterface`, `InterpreterInterface`）
- 抽象クラス: `Abstract`プレフィックスを使用（例: `AbstractToken`）
- 具象クラス: `Token`, `Node`などのサフィックス（例: `HashToken`, `HeadingNode`）

## PHPDocコメント
- 日英両方でコメントを記述するスタイル
- 例:
```php
/**
 * トークンの説明を返す
 * Returns the description of the token
 */
```

## 型宣言
- PHP 8.3の型宣言機能を使用
- 引数・戻り値の型を明示

## ディレクトリ構成パターン
- `Token/` - トークン関連（TokenInterface, AbstractToken, 具象Token）
- `Lexer/` - 字句解析器
- `Parser/` - 構文解析器（Parser, ParserContext, TokenStream）
- `Ast/Node/` - ASTノード（BaseNode, DocumentNode, 具象Node）
- `Ast/Interpreter/` - インタープリター（InterpreterInterface, 具象Interpreter）
