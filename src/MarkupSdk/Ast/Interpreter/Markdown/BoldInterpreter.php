<?php

namespace Hytmng\MarkupSdk\Ast\Interpreter\Markdown;

use Hytmng\MarkupSdk\Parser\TokenStream;
use Hytmng\MarkupSdk\Parser\ParserContext;
use Hytmng\MarkupSdk\Ast\Node\Markdown\BoldNode;
use Hytmng\MarkupSdk\Ast\Node\NodeInterface;
use Hytmng\MarkupSdk\Ast\Interpreter\InterpreterInterface;
use Hytmng\MarkupSdk\Token\AsteriskToken;

/**
 * Markdownの太字を解析するインタプリタ
 * Interpreter for parsing Markdown bold (**text**)
 */
class BoldInterpreter implements InterpreterInterface
{
    public function canInterpret(TokenStream $stream): bool
    {
        $currentToken = $stream->current();

        // 1. 最初がAsteriskTokenであること
        //    Must start with AsteriskToken
        if (!$currentToken instanceof AsteriskToken) {
            return false;
        }

        // 2. 2つ連続したAsteriskTokenであること
        //    Must have two consecutive AsteriskTokens
        if (!$stream->peek(1) instanceof AsteriskToken) {
            return false;
        }

        // 3. 閉じる ** が存在するか先読みで確認
        //    Check if closing ** exists by lookahead
        return $this->hasClosingDelimiter($stream);
    }

    /**
     * 閉じる ** が存在するかを確認する
     * Check if closing ** delimiter exists
     */
    private function hasClosingDelimiter(TokenStream $stream): bool
    {
        $lookahead = 2; // 開始の ** の後から探索
        $foundFirstAsterisk = false;

        while ($stream->peek($lookahead) !== null) {
            $token = $stream->peek($lookahead);

            if ($token instanceof AsteriskToken) {
                if ($foundFirstAsterisk) {
                    // 2つ連続した * を発見 → 閉じタグあり
                    return true;
                }
                $foundFirstAsterisk = true;
            } else {
                $foundFirstAsterisk = false;
            }

            $lookahead++;
        }

        return false;
    }

    public function interpret(TokenStream $stream, ParserContext $context): NodeInterface
    {
        // 開始の ** を消費
        // Consume opening **
        $stream->next(); // 1つ目の *
        $stream->next(); // 2つ目の *

        $node = new BoldNode();

        // 閉じる ** が見つかるまで内部をパース
        // Parse content until closing ** is found
        while (!$stream->isEnd()) {
            $currentToken = $stream->current();

            // 閉じる ** を検出
            // Detect closing **
            if ($currentToken instanceof AsteriskToken && $stream->peek(1) instanceof AsteriskToken) {
                $stream->next(); // 1つ目の * を消費
                $stream->next(); // 2つ目の * を消費
                break;
            }

            // 内部コンテンツを再帰的にパース
            // Recursively parse inner content
            $childNode = $context->parseNext();
            if ($childNode) {
                $node->addChild($childNode);
            } else {
                // どのインタプリタも処理できなかった場合、無限ループを避けるため1つ進める
                // Advance by one to avoid infinite loop if no interpreter can handle
                $stream->next();
            }
        }

        return $node;
    }

    public function getPriority(): int
    {
        return 50; // HeadingInterpreter (100) より低く、TextInterpreter (0) より高い
    }
}

