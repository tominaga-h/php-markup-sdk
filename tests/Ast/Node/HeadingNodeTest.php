<?php

declare(strict_types=1);

namespace Tests\Ast\Node;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Hytmng\MarkupSdk\Ast\Node\HeadingNode;
use Hytmng\MarkupSdk\Ast\Node\TextNode;

class HeadingNodeTest extends TestCase
{
    #[Test]
    public function typeIsHeading(): void
    {
        $node = new HeadingNode();
        $this->assertSame('heading', $node->getType());
    }

    #[Test]
    public function canSetAndGetLevelAttribute(): void
    {
        $node = new HeadingNode();
        $node->setAttribute('level', 1);

        $this->assertSame(1, $node->getAttribute('level'));
    }

    #[Test]
    public function canSetDifferentLevels(): void
    {
        $h2 = new HeadingNode();
        $h2->setAttribute('level', 2);

        $h3 = new HeadingNode();
        $h3->setAttribute('level', 3);

        $this->assertSame(2, $h2->getAttribute('level'));
        $this->assertSame(3, $h3->getAttribute('level'));
    }

    #[Test]
    public function canAddChildNode(): void
    {
        $heading = new HeadingNode();
        $text = new TextNode();
        $text->setAttribute('content', 'Title Text');

        $heading->addChild($text);

        $children = $heading->getChildren();
        $this->assertCount(1, $children);
        $this->assertInstanceOf(TextNode::class, $children[0]);
    }

    #[Test]
    public function canSetMultipleAttributes(): void
    {
        $heading = new HeadingNode();
        $heading->setAttribute('level', 2);
        $heading->setAttribute('id', 'section-1');

        $this->assertSame(2, $heading->getAttribute('level'));
        $this->assertSame('section-1', $heading->getAttribute('id'));
    }

    #[Test]
    public function getNonExistentAttributeReturnsNull(): void
    {
        $heading = new HeadingNode();

        $this->assertNull($heading->getAttribute('level'));
        $this->assertNull($heading->getAttribute('nonexistent'));
    }

    #[Test]
    public function childrenAreEmptyInitially(): void
    {
        $heading = new HeadingNode();

        $this->assertCount(0, $heading->getChildren());
    }
}
