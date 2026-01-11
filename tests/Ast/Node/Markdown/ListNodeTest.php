<?php

declare(strict_types=1);

namespace Tests\Ast\Node\Markdown;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Hytmng\MarkupSdk\Ast\Node\Markdown\ListNode;
use Hytmng\MarkupSdk\Ast\Node\Markdown\ListItemNode;
use Hytmng\MarkupSdk\Ast\Node\TextNode;

class ListNodeTest extends TestCase
{
    #[Test]
    public function typeIsList(): void
    {
        $node = new ListNode();
        $this->assertSame('list', $node->getType());
    }

    #[Test]
    public function attributeSchemaIsEmpty(): void
    {
        $node = new ListNode();
        $this->assertSame([], $node->getAttributeSchema());
    }

    #[Test]
    public function canAddListItemChild(): void
    {
        $list = new ListNode();
        $item = new ListItemNode();

        $list->addChild($item);

        $children = $list->getChildren();
        $this->assertCount(1, $children);
        $this->assertInstanceOf(ListItemNode::class, $children[0]);
    }

    #[Test]
    public function childrenAreEmptyInitially(): void
    {
        $list = new ListNode();
        $this->assertCount(0, $list->getChildren());
    }

    #[Test]
    public function toHtmlRendersUnorderedListTag(): void
    {
        $list = new ListNode();

        $item = new ListItemNode();
        $text = new TextNode();
        $text->setAttribute('content', 'Item 1');
        $item->addChild($text);

        $list->addChild($item);

        $expected = "<ul>\n<li>Item 1</li>\n</ul>\n";
        $this->assertSame($expected, $list->toHtml());
    }

    #[Test]
    public function toHtmlRendersEmptyList(): void
    {
        $list = new ListNode();

        $this->assertSame("<ul>\n</ul>\n", $list->toHtml());
    }

    #[Test]
    public function toHtmlRendersMultipleItems(): void
    {
        $list = new ListNode();

        $item1 = new ListItemNode();
        $text1 = new TextNode();
        $text1->setAttribute('content', 'Item 1');
        $item1->addChild($text1);
        $list->addChild($item1);

        $item2 = new ListItemNode();
        $text2 = new TextNode();
        $text2->setAttribute('content', 'Item 2');
        $item2->addChild($text2);
        $list->addChild($item2);

        $item3 = new ListItemNode();
        $text3 = new TextNode();
        $text3->setAttribute('content', 'Item 3');
        $item3->addChild($text3);
        $list->addChild($item3);

        $expected = "<ul>\n<li>Item 1</li>\n<li>Item 2</li>\n<li>Item 3</li>\n</ul>\n";
        $this->assertSame($expected, $list->toHtml());
    }

    #[Test]
    public function throwsExceptionWhenSettingInvalidAttribute(): void
    {
        $list = new ListNode();

        $this->expectException(\InvalidArgumentException::class);
        $list->setAttribute('type', 'ordered');
    }
}

