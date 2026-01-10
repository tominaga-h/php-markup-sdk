<?php

namespace Hytmng\MarkupSdk\Ast\Interpreter;

use Hytmng\MarkupSdk\Parser\TokenStream;
use Hytmng\MarkupSdk\Parser\ParserContext;
use Hytmng\MarkupSdk\Ast\Node\NodeInterface;

/**
 * トークンの並びを解析し、ASTノードに変換するためのインタープリタを構成するインターフェース
 * Interface for the interpreter that interprets token sequences and converts them into AST nodes
 */
interface InterpreterInterface {
    /**
     * 現在のトークン列から、このインタプリタが処理を開始すべきか判定する
     * Determines whether this interpreter should start processing from the current token stream
     */
    public function canInterpret(TokenStream $stream): bool;

    /**
     * トークンを消費し、対応するASTノードを生成して返す
     * Consumes tokens and returns the corresponding AST node
     */
    public function interpret(TokenStream $stream, ParserContext $context): NodeInterface;

    /**
     * インタプリタの優先順位を取得する（値が大きいほど先に実行される）
     * Returns the priority of the interpreter (higher values are executed first)
     */
    public function getPriority(): int;
}
