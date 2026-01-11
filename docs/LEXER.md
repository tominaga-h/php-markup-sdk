# Lexer - 字句解析器の仕組み

このドキュメントでは、マークアップ SDK の Lexer（字句解析器）処理について解説します。

## 全体アーキテクチャ

Lexer は入力文字列を**トークン**と呼ばれる最小単位に分割します。トークンはパーサーが解析しやすい形式に変換された文字列の断片です。

```mermaid
flowchart LR
    subgraph Input
        A[文字列]
    end

    subgraph Lexer
        B[tokenize]
        C[登録済みトークン]
        D[パターンマッチング]
    end

    subgraph Output
        E["TokenInterface[]"]
    end

    A --> B
    B --> C
    C --> D
    D --> B
    B --> E
```

## 各クラスの役割

### Lexer

入力文字列をトークンの配列に変換するメインクラス。

| メソッド           | 機能                       |
| ------------------ | -------------------------- |
| `registerToken()`  | 単一のトークンを登録       |
| `registerTokens()` | 複数のトークンを一括登録   |
| `tokenize()`       | 文字列をトークン配列に分割 |

### TokenInterface

すべてのトークンが実装するインターフェース。

| メソッド           | 機能                                           |
| ------------------ | ---------------------------------------------- |
| `getPattern()`     | トークンを抽出するための正規表現パターンを返す |
| `getValue()`       | トークンの実際の値（文字列）を返す             |
| `setValue()`       | トークンの実際の値（文字列）を設定する         |
| `getDescription()` | トークンの説明を返す                           |

### AbstractToken

`TokenInterface` の基本実装を提供する抽象クラス。`getValue()` と `setValue()` の共通実装を持ちます。

## トークン一覧

現在実装されているトークンの一覧です。

| トークンクラス  | パターン | 説明                               |
| --------------- | -------- | ---------------------------------- |
| `AsteriskToken` | `/^\*/`  | アスタリスク（`*`）記号            |
| `HashToken`     | `/^#/`   | ハッシュ（`#`）記号                |
| `HyphenToken`   | `/^-/`   | ハイフン（`-`）記号                |
| `SpaceToken`    | `/^ /`   | スペース記号                       |
| `NewlineToken`  | `/^\R/`  | 改行文字（`\n`, `\r\n` など）      |
| `TextToken`     | なし     | 上記以外の文字列（フォールバック） |

## tokenize の処理フロー

```mermaid
flowchart TD
    A[開始] --> B{入力文字列が残っている?}
    B -->|Yes| C[登録済みトークンを順番にチェック]
    C --> D{パターンがマッチ?}
    D -->|Yes| E[テキストバッファを確定]
    E --> F[マッチしたトークンを追加]
    F --> G[オフセットを更新]
    G --> B
    D -->|No| H[次のトークンをチェック]
    H --> I{全トークンをチェック済み?}
    I -->|No| D
    I -->|Yes| J[現在の文字をテキストバッファに追加]
    J --> K[オフセットを1進める]
    K --> B
    B -->|No| L{テキストバッファが空でない?}
    L -->|Yes| M[TextTokenとして追加]
    M --> N[終了]
    L -->|No| N
```

### 処理の詳細

1. **パターンマッチング**: 登録されたトークンのパターンを順番に試行
2. **テキストバッファ**: マッチしない文字は一時的にバッファに蓄積
3. **TextToken 生成**: 記号が見つかった時点でバッファの内容を `TextToken` として確定
4. **トークン複製**: マッチしたトークンは `clone` して値を設定

## 具体例

### 入力

```
Hello **world**
```

### トークン化の過程

```mermaid
sequenceDiagram
    participant L as Lexer
    participant B as TextBuffer
    participant T as Tokens

    Note over L: offset=0, "H"
    L->>B: "H" を追加
    Note over L: offset=1, "e"
    L->>B: "e" を追加
    Note over L: offset=2-5
    L->>B: "llo " を追加
    Note over L: offset=6, "*" がマッチ
    B->>T: TextToken("Hello ")
    L->>T: AsteriskToken("*")
    Note over L: offset=7, "*" がマッチ
    L->>T: AsteriskToken("*")
    Note over L: offset=8-12
    L->>B: "world" を追加
    Note over L: offset=13, "*" がマッチ
    B->>T: TextToken("world")
    L->>T: AsteriskToken("*")
    Note over L: offset=14, "*" がマッチ
    L->>T: AsteriskToken("*")
```

### 出力

```mermaid
flowchart LR
    T1["TextToken: 'Hello '"] --> T2["AsteriskToken: '*'"]
    T2 --> T3["AsteriskToken: '*'"]
    T3 --> T4["TextToken: 'world'"]
    T4 --> T5["AsteriskToken: '*'"]
    T5 --> T6["AsteriskToken: '*'"]
```

## 拡張性

新しいマークアップ記号を追加する場合は、**新しいトークンクラスを実装して登録するだけ**で対応できます。

```php
// 新しいトークンを実装
class TildeToken extends AbstractToken
{
    public function getPattern(): string
    {
        return '/^~/';
    }

    public function getDescription(): string
    {
        return 'チルダ(~)記号のトークン';
    }
}

// Lexer に登録
$lexer->registerToken(new TildeToken());
```

### 注意点

- トークンの登録順序が重要です。先に登録されたトークンが優先的にマッチします
- `TextToken` は特殊なトークンで、パターンを持たず、他のトークンにマッチしなかった文字列を収集します
- 正規表現パターンは必ず `^`（文字列の先頭）から始める必要があります
