<?php

namespace Hytmng\MarkupSdk\Ast\Node;

use Hytmng\MarkupSdk\Ast\Node\NodeInterface;

/**
 * 全てのノードの基底となる抽象クラス
 * Base abstract class for all nodes
 */
abstract class BaseNode implements NodeInterface {

    /** @var array<string, mixed> */
    protected array $attributes = [];

    /** @var NodeInterface[] */
    protected array $children = [];

    public function setAttribute(string $key, mixed $value): void {
        $this->attributes[$key] = $value;
    }

    public function getAttribute(string $key): mixed {
        return $this->attributes[$key] ?? null;
    }

    public function addChild(NodeInterface $node): void {
        $this->children[] = $node;
    }

    public function getChildren(): array {
        return $this->children;
    }

    /**
     * 各具象クラスで独自の型名を返す必要がある
     * Each concrete class must return its own type name
     */
    abstract public function getType(): string;
}
