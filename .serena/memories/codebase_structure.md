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
├── vendor/                  # Composerパッケージ
├── composer.json
├── composer.lock
├── phpunit.xml
├── Makefile
├── Dockerfile
├── docker-compose.yml
└── REAMDE.md               # ※ファイル名にタイポあり（README.md が正しい）
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
