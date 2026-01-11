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
        $schema = $this->getAttributeSchema();

        // 1. 属性名が許可されているか
        //    Check if the attribute name is allowed
        if (!array_key_exists($key, $schema)) {
            throw new \InvalidArgumentException(
                sprintf("属性 '%s' は許可されていないノードタイプ '%s' です。", $key, $this->getType()) .
                sprintf("Attribute '%s' is not defined in schema for node type '%s'", $key, $this->getType())
            );
        }

        // 2. 型が一致しているか (gettypeによる簡易チェック)
        //    Check if the type matches (simple check using gettype)
        $expectedType = $schema[$key];
        $actualType = gettype($value);

        if ($actualType !== $expectedType) {
            throw new \InvalidArgumentException(
                sprintf("属性 '%s' は型 %s でなければなりません。%s が指定されています。", $key, $expectedType, $actualType) .
                sprintf("Attribute '%s' must be of type %s, %s given.", $key, $expectedType, $actualType)
            );
        }

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

    abstract public function getType(): string;

    abstract public function getAttributeSchema(): array;

    abstract public function toHtml(): string;

    /**
     * 子要素のHTMLを結合して返すヘルパーメソッド
     * Helper method to concatenate the HTML of child elements
     */
    protected function renderChildren(): string {
        $html = "";
        foreach ($this->children as $child) {
            $html .= $child->toHtml();
        }
        return $html;
    }
}
