# コードベース構造

```
php-markup-sdk/
├── bin/
│   └── console              # サンプル実行用エントリーポイント
├── docs/
│   └── OVERVIEW.md          # プロジェクト概要ドキュメント
├── src/MarkupSdk/
│   ├── Token/               # トークン定義
│   │   ├── TokenInterface.php
│   │   ├── AbstractToken.php
│   │   ├── TextToken.php
│   │   ├── SpaceToken.php
│   │   ├── HashToken.php
│   │   ├── AsteriskToken.php
│   │   └── NewlineToken.php
│   ├── Lexer/               # 字句解析
│   │   └── Lexer.php
│   ├── Parser/              # 構文解析
│   │   ├── Parser.php
│   │   ├── ParserContext.php
│   │   └── TokenStream.php
│   └── Ast/                 # 抽象構文木
│       ├── Node/            # ノード定義
│       │   ├── NodeInterface.php
│       │   ├── BaseNode.php
│       │   ├── DocumentNode.php
│       │   ├── TextNode.php
│       │   └── HeadingNode.php
│       └── Interpreter/     # インタープリター
│           ├── InterpreterInterface.php
│           ├── HeadingInterpreter.php
│           └── TextInterpreter.php
├── tests/                   # テストディレクトリ
│   ├── Token/               # トークンテスト
│   │   ├── AsteriskTokenTest.php
│   │   ├── NewlineTokenTest.php
│   │   ├── HashTokenTest.php
│   │   ├── TextTokenTest.php
│   │   └── SpaceTokenTest.php
│   ├── Lexer/               # Lexerテスト
│   │   └── LexerTest.php
│   ├── Parser/              # Parserテスト
│   │   ├── ParserTest.php
│   │   ├── ParserContextTest.php
│   │   └── TokenStreamTest.php
│   ├── Ast/
│   │   ├── Node/            # Nodeテスト
│   │   │   ├── HeadingNodeTest.php
│   │   │   ├── DocumentNodeTest.php
│   │   │   └── TextNodeTest.php
│   │   └── Interpreter/     # Interpreterテスト
│   │       ├── HeadingInterpreterTest.php
│   │       └── TextInterpreterTest.php
│   └── Integration/         # 統合テスト
│       └── MarkupParsingTest.php
├── vendor/                  # Composerパッケージ
├── composer.json
├── composer.lock
├── phpunit.xml
├── coverage.xml             # カバレッジレポート
├── Makefile
├── Dockerfile
├── docker-compose.yml
├── php.ini
└── README.md
```

## アーキテクチャ概要

```
入力文字列
    ↓
[Lexer] ──(Token登録)──→ TokenStream
    ↓
[Parser] ──(Interpreter登録)──→ AST (DocumentNode)
    ↓
出力（任意フォーマット）
```

## 主要クラス関係
- `TokenInterface` ← `AbstractToken` ← 具象Token（HashToken等）
- `NodeInterface` ← `BaseNode` ← 具象Node（HeadingNode等）
- `InterpreterInterface` ← 具象Interpreter（HeadingInterpreter等）

## テスト構成
テストは `tests/` ディレクトリに src/ のディレクトリ構造をミラーリングして配置。
- 単体テスト: Token, Lexer, Parser, Ast/Node, Ast/Interpreter
- 統合テスト: Integration/MarkupParsingTest.php