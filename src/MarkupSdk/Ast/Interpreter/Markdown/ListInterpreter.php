<?php

namespace Hytmng\MarkupSdk\Ast\Interpreter\Markdown;

use Hytmng\MarkupSdk\Parser\TokenStream;
use Hytmng\MarkupSdk\Parser\ParserContext;
use Hytmng\MarkupSdk\Ast\Node\Markdown\ListNode;
use Hytmng\MarkupSdk\Ast\Node\Markdown\ListItemNode;
use Hytmng\MarkupSdk\Ast\Node\NodeInterface;
use Hytmng\MarkupSdk\Ast\Interpreter\InterpreterInterface;
use Hytmng\MarkupSdk\Token\AsteriskToken;
use Hytmng\MarkupSdk\Token\HyphenToken;
use Hytmng\MarkupSdk\Token\SpaceToken;
use Hytmng\MarkupSdk\Token\NewlineToken;

/**
 * Markdownの順序なしリストを解析するインタプリタ
 * Interpreter for parsing Markdown unordered lists
 */
class ListInterpreter implements InterpreterInterface
{
    public function canInterpret(TokenStream $stream): bool
    {
        $currentToken = $stream->current();

        // 1. 最初がAsteriskTokenまたはHyphenTokenであること。
        //    Must start with AsteriskToken or HyphenToken
        if (!$this->isListMarker($currentToken)) {
            return false;
        }

        // 2. マーカーの後にSpaceTokenが続くこと。
        //    Check that the marker is followed by SpaceToken
        return $stream->peek(1) instanceof SpaceToken;
    }

    public function interpret(TokenStream $stream, ParserContext $context): NodeInterface
    {
        $node = new ListNode();

        // 連続するリスト項目をパースする
        // Parse consecutive list items
        while (!$stream->isEnd() && $this->isListItemStart($stream)) {
            $itemNode = $this->parseListItem($stream, $context);
            $node->addChild($itemNode);
        }

        return $node;
    }

    public function getPriority(): int
    {
        return 100;
    }

    /**
     * 現在のトークンがリストマーカー（* または -）かどうかを判定
     * Check if the current token is a list marker (* or -)
     */
    private function isListMarker(mixed $token): bool
    {
        return $token instanceof AsteriskToken || $token instanceof HyphenToken;
    }

    /**
     * 現在の位置がリスト項目の開始かどうかを判定
     * Check if the current position is the start of a list item
     */
    private function isListItemStart(TokenStream $stream): bool
    {
        $currentToken = $stream->current();

        if (!$this->isListMarker($currentToken)) {
            return false;
        }

        return $stream->peek(1) instanceof SpaceToken;
    }

    /**
     * 単一のリスト項目をパースする
     * Parse a single list item
     */
    private function parseListItem(TokenStream $stream, ParserContext $context): ListItemNode
    {
        $itemNode = new ListItemNode();

        // リストマーカーをスキップ
        // Skip the list marker
        $stream->next();

        // 必須のスペースをスキップ
        // Skip the mandatory space
        if ($stream->current() instanceof SpaceToken) {
            $stream->next();
        }

        // 改行またはストリーム終了まで、その行の内容をパースする
        // Parse the content of the line until a newline or end of stream
        while (!$stream->isEnd()) {
            $currentToken = $stream->current();

            // 改行を見つけたら、その行の解析は終了
            // Stop parsing the line when a newline is encountered
            if ($currentToken instanceof NewlineToken) {
                $stream->next(); // 改行トークン自体も消費する。The newline token itself is also consumed.
                break;
            }

            // 行内のテキストや強調構文などを再帰的に取得
            // Recursively get text, emphasis, etc., within the line
            $childNode = $context->parseNext();
            if ($childNode) {
                $itemNode->addChild($childNode);
            } else {
                // どのインタプリタも処理できなかった場合、無限ループを避けるため1つ進める
                // If no interpreter can process, advance by one to avoid infinite loop
                $stream->next();
            }
        }

        return $itemNode;
    }
}

