# php-markup-sdk プロジェクト概要

## 目的
独自のマークアップ言語を構築するためのSDK（デベロッパー・プラットフォーム）。
開発者が「トークンの定義」と「解釈のロジック」を実装することで、堅牢な抽象構文木（AST）を構築し、任意のフォーマットへ出力可能にする。

## 技術スタック
- **言語**: PHP >= 8.3
- **テスト**: PHPUnit 12.0
- **コンテナ**: Docker / docker-compose
- **ライセンス**: MIT

## コア・コンセプト
1. **最小単位トークン**: Lexerは記号（#や*）を最小単位（基本は1文字）で切り出し、意味づけは行わない
2. **解釈（Interpret）の分離**: トークンの並びから「見出し」や「太字」といった意味を見出すロジックは、Parser側のInterpreterが担当
3. **再帰的構造**: ParserContextを通じて、ノードの中身をさらにパースする「入れ子構造」を標準サポート

## 主要コンポーネント
- **Lexer**: 文字列をTokenStreamに変換（字句解析）
- **Token**: 意味を持たない純粋な文字情報のコンテナ
- **Parser/ParserEngine**: 登録されたInterpreterを優先度順に実行しASTを構築
- **Interpreter**: トークンを消費しNodeを生成
- **AST Node**: 抽象構文木のノード（BaseNode, DocumentNodeなど）

## SDK利用者のワークフロー
1. Token定義: AbstractTokenを継承して記号を作成
2. Node定義: BaseNodeを継承して構造体を作成
3. Interpreter実装: InterpretInterfaceを実装して解釈ロジックを記述
4. 登録と実行: EngineにTokenとInterpreterを登録し、文字列を入力してASTを得る
