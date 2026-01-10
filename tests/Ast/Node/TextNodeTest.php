<?php

declare(strict_types=1);

namespace Tests\Ast\Node;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Hytmng\MarkupSdk\Ast\Node\TextNode;

class TextNodeTest extends TestCase
{
    #[Test]
    public function typeIsText(): void
    {
        $node = new TextNode();
        $this->assertSame('text', $node->getType());
    }

    #[Test]
    public function canSetAndGetContentAttribute(): void
    {
        $node = new TextNode();
        $node->setAttribute('content', 'Hello World');

        $this->assertSame('Hello World', $node->getAttribute('content'));
    }

    #[Test]
    public function canSetJapaneseContent(): void
    {
        $node = new TextNode();
        $node->setAttribute('content', 'こんにちは世界');

        $this->assertSame('こんにちは世界', $node->getAttribute('content'));
    }

    #[Test]
    public function canSetEmptyStringContent(): void
    {
        $node = new TextNode();
        $node->setAttribute('content', '');

        $this->assertSame('', $node->getAttribute('content'));
    }

    #[Test]
    public function canSetMultipleAttributes(): void
    {
        $node = new TextNode();
        $node->setAttribute('content', 'Sample text');
        $node->setAttribute('bold', true);

        $this->assertSame('Sample text', $node->getAttribute('content'));
        $this->assertTrue($node->getAttribute('bold'));
    }

    #[Test]
    public function getNonExistentAttributeReturnsNull(): void
    {
        $node = new TextNode();

        $this->assertNull($node->getAttribute('content'));
        $this->assertNull($node->getAttribute('nonexistent'));
    }

    #[Test]
    public function canAddChildNode(): void
    {
        $parent = new TextNode();
        $child = new TextNode();
        $child->setAttribute('content', 'Child text');

        $parent->addChild($child);

        $children = $parent->getChildren();
        $this->assertCount(1, $children);
        $this->assertSame('Child text', $children[0]->getAttribute('content'));
    }

    #[Test]
    public function childrenAreEmptyInitially(): void
    {
        $node = new TextNode();

        $this->assertCount(0, $node->getChildren());
    }
}
