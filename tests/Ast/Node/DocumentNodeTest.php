<?php

declare(strict_types=1);

namespace Tests\Ast\Node;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Hytmng\MarkupSdk\Ast\Node\DocumentNode;
use Hytmng\MarkupSdk\Ast\Node\TextNode;
use Hytmng\MarkupSdk\Ast\Node\Markdown\HeadingNode;

class DocumentNodeTest extends TestCase
{
    #[Test]
    public function typeIsDocument(): void
    {
        $node = new DocumentNode();
        $this->assertSame('document', $node->getType());
    }

    #[Test]
    public function canAddChildNode(): void
    {
        $document = new DocumentNode();
        $textNode = new TextNode();

        $document->addChild($textNode);

        $this->assertCount(1, $document->getChildren());
        $this->assertSame($textNode, $document->getChildren()[0]);
    }

    #[Test]
    public function canAddMultipleChildNodes(): void
    {
        $document = new DocumentNode();
        $heading = new HeadingNode();
        $text = new TextNode();

        $document->addChild($heading);
        $document->addChild($text);

        $children = $document->getChildren();
        $this->assertCount(2, $children);
        $this->assertInstanceOf(HeadingNode::class, $children[0]);
        $this->assertInstanceOf(TextNode::class, $children[1]);
    }

    #[Test]
    public function throwsExceptionWhenSettingAnyAttribute(): void
    {
        $document = new DocumentNode();

        $this->expectException(\InvalidArgumentException::class);
        $document->setAttribute('title', 'My Document');
    }

    #[Test]
    public function schemaIsEmpty(): void
    {
        $document = new DocumentNode();

        $this->assertSame([], $document->getAttributeSchema());
    }

    #[Test]
    public function childrenAreEmptyInitially(): void
    {
        $document = new DocumentNode();

        $this->assertCount(0, $document->getChildren());
    }
}
