<?php

namespace Hytmng\MarkupSdk\Parser;

use Hytmng\MarkupSdk\Ast\Node\NodeInterface;
use Hytmng\MarkupSdk\Ast\Node\DocumentNode;
use Hytmng\MarkupSdk\Ast\Interpreter\InterpreterInterface;
use Hytmng\MarkupSdk\Parser\TokenStream;
use Hytmng\MarkupSdk\Parser\ParserContext;

/**
 * トークンストリームを解析してASTを構築するメインクラス
 * Main class that analyzes the token stream and builds the AST
 */
class Parser
{
    /** @var InterpreterInterface[] */
    private array $interpreters = [];

    /**
     * インタプリタを登録する
     * Registers an interpreter
     */
    public function registerInterpreter(InterpreterInterface $interpreter): void
    {
        $this->interpreters[] = $interpreter;

        // 優先度が高い順（降順）にソート
        // Sort in descending order of priority
        usort($this->interpreters, function($a, $b) {
            return $b->getPriority() <=> $a->getPriority();
        });
    }

    public function registerInterpreters(array $interpreters): void
    {
        foreach ($interpreters as $interpreter) {
            if (!$interpreter instanceof InterpreterInterface) {
                throw new \InvalidArgumentException('インタプリタはInterpreterInterfaceを実装したクラスのインスタンスでなければなりません。Interpreter must be an instance of InterpreterInterface');
            }
            $this->registerInterpreter($interpreter);
        }
    }

    /**
     * 全てのトークンを解析し、ルートノードを返す
     * Parses all tokens and returns the root node
     */
    public function parse(TokenStream $stream): NodeInterface
    {
        $document = new DocumentNode(); // ルートとなるノード / Root node
        $context = new ParserContext($this, $stream);

        while (!$stream->isEnd()) {
            $node = $this->parseStep($stream, $context);
            if ($node) {
                $document->addChild($node);
            } else {
                // どのインタプリタもマッチしない場合はトークンを1つ飛ばす
                // Advance the stream if no interpreter matches
                $stream->next();
            }
        }

        return $document;
    }

    /**
     * ストリームの現在地点から1つのノードを解析する
     * Parses a single node from the current position of the stream
     */
    public function parseStep(TokenStream $stream, ParserContext $context): ?NodeInterface
    {
        foreach ($this->interpreters as $interpreter) {
            if ($interpreter->canInterpret($stream)) {
                return $interpreter->interpret($stream, $context);
            }
        }
        return null;
    }
}
