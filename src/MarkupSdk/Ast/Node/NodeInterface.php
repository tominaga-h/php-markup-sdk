<?php

namespace Hytmng\MarkupSdk\Ast\Node;

/**
 * 抽象構文木（AST）のノードを表すインターフェース
 * Interface representing a node in the Abstract Syntax Tree (AST)
 */
interface NodeInterface {
    /**
     * ノードの種類を返す（例: "heading", "paragraph"）
     * Returns the type of the node (e.g., "heading", "paragraph")
     */
    public function getType(): string;

    /**
     * ノードに属性を設定する
     * Sets an attribute to the node
     */
    public function setAttribute(string $key, mixed $value): void;

    /**
     * ノードの属性を取得する
     * Gets an attribute from the node
     */
    public function getAttribute(string $key): mixed;

    /**
     * 子ノードを追加する
     * Adds a child node
     */
    public function addChild(NodeInterface $node): void;

    /**
     * 全ての子ノードを返す
     * Returns all child nodes
     * @return NodeInterface[]
     */
    public function getChildren(): array;

    /**
     * ノードをHTMLに変換する
     * Converts the node to HTML
     */
    public function toHtml(): string;
}
