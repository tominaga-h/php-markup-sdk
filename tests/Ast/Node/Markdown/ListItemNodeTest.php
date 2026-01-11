<?php

declare(strict_types=1);

namespace Tests\Ast\Node\Markdown;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Hytmng\MarkupSdk\Ast\Node\Markdown\ListItemNode;
use Hytmng\MarkupSdk\Ast\Node\TextNode;

class ListItemNodeTest extends TestCase
{
    #[Test]
    public function typeIsListItem(): void
    {
        $node = new ListItemNode();
        $this->assertSame('list_item', $node->getType());
    }

    #[Test]
    public function attributeSchemaIsEmpty(): void
    {
        $node = new ListItemNode();
        $this->assertSame([], $node->getAttributeSchema());
    }

    #[Test]
    public function canAddChildNode(): void
    {
        $listItem = new ListItemNode();
        $text = new TextNode();
        $text->setAttribute('content', 'Item text');

        $listItem->addChild($text);

        $children = $listItem->getChildren();
        $this->assertCount(1, $children);
        $this->assertInstanceOf(TextNode::class, $children[0]);
    }

    #[Test]
    public function childrenAreEmptyInitially(): void
    {
        $listItem = new ListItemNode();
        $this->assertCount(0, $listItem->getChildren());
    }

    #[Test]
    public function toHtmlRendersListItemTag(): void
    {
        $listItem = new ListItemNode();
        $text = new TextNode();
        $text->setAttribute('content', 'Item text');
        $listItem->addChild($text);

        $this->assertSame("<li>Item text</li>\n", $listItem->toHtml());
    }

    #[Test]
    public function toHtmlRendersEmptyListItem(): void
    {
        $listItem = new ListItemNode();

        $this->assertSame("<li></li>\n", $listItem->toHtml());
    }

    #[Test]
    public function toHtmlRendersMultipleChildren(): void
    {
        $listItem = new ListItemNode();

        $text1 = new TextNode();
        $text1->setAttribute('content', 'First ');
        $listItem->addChild($text1);

        $text2 = new TextNode();
        $text2->setAttribute('content', 'Second');
        $listItem->addChild($text2);

        $this->assertSame("<li>First Second</li>\n", $listItem->toHtml());
    }

    #[Test]
    public function throwsExceptionWhenSettingInvalidAttribute(): void
    {
        $listItem = new ListItemNode();

        $this->expectException(\InvalidArgumentException::class);
        $listItem->setAttribute('invalid', 'value');
    }
}

