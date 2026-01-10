<?php

namespace Hytmng\MarkupSdk\Ast\Interpreter;

use Hytmng\MarkupSdk\Parser\TokenStream;
use Hytmng\MarkupSdk\Parser\ParserContext;
use Hytmng\MarkupSdk\Ast\Node\TextNode;
use Hytmng\MarkupSdk\Ast\Node\NodeInterface;
use Hytmng\MarkupSdk\Ast\Interpreter\InterpreterInterface;
use Hytmng\MarkupSdk\Token\TextToken;
use Hytmng\MarkupSdk\Token\SpaceToken;

class TextInterpreter implements InterpreterInterface
{
    public function canInterpret(TokenStream $stream): bool
    {
        $token = $stream->current();

        // 明示的にテキストとして扱って良いトークンのみを許可する（ホワイトリスト）
        // Only allow tokens explicitly identified as text-safe (whitelist)
        return (
            $token instanceof TextToken ||
            $token instanceof SpaceToken
        );
    }

    public function interpret(TokenStream $stream, ParserContext $context): NodeInterface
    {
        $aggregatedContent = "";

        // 次のトークンもテキストとして解釈できる限り、連結を続ける
        // Keep concatenating as long as the next token can be interpreted as text
        while (!$stream->isEnd() && $this->canInterpret($stream)) {
            $aggregatedContent .= $stream->current()->getValue();
            $stream->next();
        }

        $node = new TextNode();
        $node->setAttribute('content', $aggregatedContent);

        return $node;
    }

    public function getPriority(): int
    {
        return 0; // 最低優先度。 The lowest priority.
    }
}
