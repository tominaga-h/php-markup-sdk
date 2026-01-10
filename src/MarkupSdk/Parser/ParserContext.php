<?php
namespace Hytmng\MarkupSdk\Parser;

use Hytmng\MarkupSdk\Parser\Parser;
use Hytmng\MarkupSdk\Parser\TokenStream;
use Hytmng\MarkupSdk\Ast\Node\NodeInterface;

/**
 * パースの実行コンテキストを管理するクラス
 * Class that manages the execution context of parsing
 */
class ParserContext {
    private Parser $parser;
    private TokenStream $stream;

    public function __construct(Parser $parser, TokenStream $stream)
    {
        $this->parser = $parser;
        $this->stream = $stream;
    }

    /**
     * 次のノードを1つ解析して返す（再帰的パースに使用）
     * Parses and returns the next single node (used for recursive parsing)
     *
     * @return NodeInterface|null
     */
    public function parseNext(): ?NodeInterface
    {
        return $this->parser->parseStep($this->stream, $this);
    }

    /**
     * 特定の条件が満たされるまで解析を繰り返し、ノードの配列を返す
     * Repeatedly parses until a specific condition is met, returning an array of nodes
     *
     * @param callable $stopCondition
     * @return NodeInterface[]
     */
    public function parseUntil(callable $stopCondition): array
    {
        $nodes = [];
        while (!$this->stream->isEnd() && !$stopCondition($this->stream)) {
            $node = $this->parseNext();
            if ($node) {
                $nodes[] = $node;
            }
        }
        return $nodes;
    }
}
