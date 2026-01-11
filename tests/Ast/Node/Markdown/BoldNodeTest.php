<?php

declare(strict_types=1);

namespace Tests\Ast\Node\Markdown;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Hytmng\MarkupSdk\Ast\Node\Markdown\BoldNode;
use Hytmng\MarkupSdk\Ast\Node\TextNode;

class BoldNodeTest extends TestCase
{
    #[Test]
    public function typeIsBold(): void
    {
        $node = new BoldNode();
        $this->assertSame('bold', $node->getType());
    }

    #[Test]
    public function attributeSchemaIsEmpty(): void
    {
        $node = new BoldNode();
        $this->assertSame([], $node->getAttributeSchema());
    }

    #[Test]
    public function canAddChildNode(): void
    {
        $bold = new BoldNode();
        $text = new TextNode();
        $text->setAttribute('content', 'Bold Text');

        $bold->addChild($text);

        $children = $bold->getChildren();
        $this->assertCount(1, $children);
        $this->assertInstanceOf(TextNode::class, $children[0]);
    }

    #[Test]
    public function childrenAreEmptyInitially(): void
    {
        $bold = new BoldNode();

        $this->assertCount(0, $bold->getChildren());
    }

    #[Test]
    public function toHtmlRendersStrongTag(): void
    {
        $bold = new BoldNode();
        $text = new TextNode();
        $text->setAttribute('content', 'Bold Text');
        $bold->addChild($text);

        $this->assertSame('<strong>Bold Text</strong>', $bold->toHtml());
    }

    #[Test]
    public function toHtmlRendersEmptyStrongTagWhenNoChildren(): void
    {
        $bold = new BoldNode();

        $this->assertSame('<strong></strong>', $bold->toHtml());
    }

    #[Test]
    public function toHtmlRendersMultipleChildren(): void
    {
        $bold = new BoldNode();

        $text1 = new TextNode();
        $text1->setAttribute('content', 'Hello ');
        $bold->addChild($text1);

        $text2 = new TextNode();
        $text2->setAttribute('content', 'World');
        $bold->addChild($text2);

        $this->assertSame('<strong>Hello World</strong>', $bold->toHtml());
    }

    #[Test]
    public function throwsExceptionWhenSettingAnyAttribute(): void
    {
        $bold = new BoldNode();

        $this->expectException(\InvalidArgumentException::class);
        $bold->setAttribute('unknown', 'value');
    }
}

