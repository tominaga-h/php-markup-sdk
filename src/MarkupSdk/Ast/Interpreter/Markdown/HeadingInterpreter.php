<?php

namespace Hytmng\MarkupSdk\Ast\Interpreter\Markdown;

use Hytmng\MarkupSdk\Parser\TokenStream;
use Hytmng\MarkupSdk\Parser\ParserContext;
use Hytmng\MarkupSdk\Ast\Node\Markdown\HeadingNode;
use Hytmng\MarkupSdk\Ast\Node\NodeInterface;
use Hytmng\MarkupSdk\Ast\Interpreter\InterpreterInterface;
use Hytmng\MarkupSdk\Token\HashToken;
use Hytmng\MarkupSdk\Token\SpaceToken;
use Hytmng\MarkupSdk\Token\TextToken;
use Hytmng\MarkupSdk\Token\NewlineToken;

/**
 * Markdownの見出しを解析するインタプリタ
 * Interpreter for parsing Markdown headings
 */
class HeadingInterpreter implements InterpreterInterface {

    public function canInterpret(TokenStream $stream): bool
    {
        $currentToken = $stream->current();

        // 1. 最初がHashTokenであること。
        //    Must start with HashToken
        if (!$currentToken instanceof HashToken) {
            return false;
        }
        // 2. HashTokenの連続の後にSpaceTokenが続くこと。
        //    Check that the consecutive HashTokens are followed by SpaceTokens
        $lookahead = 1;
        while ($stream->peek($lookahead) instanceof HashToken) {
            $lookahead++;
        }

        return $stream->peek($lookahead) instanceof SpaceToken;
    }

    public function interpret(TokenStream $stream, ParserContext $context): NodeInterface
    {
        $level = 0;

        // 連続するハッシュをカウントしてレベルを決定
        // Count consecutive hashes to determine the level
        while ($stream->current() instanceof HashToken) {
            $level++;
            $stream->next();
        }

        // 必須のスペースをスキップ
        // Skip the mandatory space
        if ($stream->current() instanceof SpaceToken) {
            $stream->next();
        }

        $node = new HeadingNode();
        $node->setAttribute('level', $level);

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
                $node->addChild($childNode);
            } else {
                // どのインタプリタも処理できなかった場合、無限ループを避けるため1つ進める
                $stream->next();
            }
        }

        return $node;
    }

    public function getPriority(): int
    {
        return 100;
    }
}

