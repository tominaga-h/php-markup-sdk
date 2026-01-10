# php-markup-sdk

## 1. プロジェクト概要

独自のマークアップ言語を構築するためのデベロッパー・プラットフォーム（SDK）。

開発者が「トークンの定義」と「解釈のロジック」を実装することで、堅牢な抽象構文木（AST）を構築し、任意のフォーマットへ出力可能にする。

## 2. コア・コンセプト

- **最小単位トークン**: Lexer は記号（#や\*）を最小単位（基本は 1 文字）で切り出し、意味づけは行わない。

- **解釈（Interpret）の分離**: トークンの並びから「見出し」や「太字」といった意味を見出すロジックは、Parser 側の `Interpreter` が担当する。

- **再帰的構造**: `ParserContext` を通じて、ノードの中身をさらにパースする「入れ子構造」を標準サポートする。

## 3. 主要コンポーネント定義

### A. Lexer (字句解析)

- **役割**: 文字列を `TokenStream` に変換する。

- **TextToken の自動集約**: 登録された記号トークンにマッチしない文字は、自動的に `TextToken` としてまとめ、効率化を図る。

- **自己定義トークン**: 各 Token クラスが自身の抽出用正規表現（`getPattern`）を持つ。

### B. Token (字句)

- **TokenInterface**: `getPattern()`, `getValue()`, `setValue()`, `getDescription()` を定義。

- **役割**: 意味を持たない純粋な文字情報のコンテナ。

### C. Parser (構文解析)

- **ParserEngine**: 登録された `Interpreter` を優先度順に実行し、AST を構築する司令塔。

- **ParserContext**: Interpreter が再帰的にパースを行うための窓口。`parseNext()` などの API を提供。

- **InterpretInterface**:

  - `canInterpret(stream)`: 現在のトークン列から自身が処理すべきか判定。

  - `interpret(stream, context)`: トークンを消費し、Node を生成。

### D. AST (抽象構文木)

- **BaseNode**: 全てのノードの基底クラス。`$attributes` による柔軟なデータ保持と、`$children` による親子関係の管理。

- **DocumentNode**: ツリーのルートとなる特殊なノード。

## 4. 開発フロー (SDK 利用者のワークフロー)

1. **Token 定義**: `AbstractToken` を継承して、使用する記号（例：`HashToken`）を作成。

2. **Node 定義**: `BaseNode` を継承して、構造体（例：`HeadingNode`）を作成。

3. **Interpreter 実装**: `InterpretInterface` を実装し、「`#` が N 個続いたら `HeadingNode` にする」といった解釈ロジックを記述。

4. **登録と実行**: SDK の Engine に上記を登録し、文字列を入力して AST を得る。

## 5. 設計上の重要ルール

- **最長一致**: Lexer はパターンの長いトークンから優先的にマッチングを試みる。

- **コンテキストの優先順位**: パーサー側でも `Interpreter` ごとに優先度（Priority）を持ち、構文の強弱を管理する。
