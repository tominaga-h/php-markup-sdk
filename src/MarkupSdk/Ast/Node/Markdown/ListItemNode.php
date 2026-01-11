<?php

namespace Hytmng\MarkupSdk\Ast\Node\Markdown;

use Hytmng\MarkupSdk\Ast\Node\BaseNode;

class ListItemNode extends BaseNode
{
    public function getType(): string
    {
        return 'list_item';
    }

    public function getAttributeSchema(): array
    {
        return []; // 属性なし。No attributes.
    }

    public function toHtml(): string
    {
        return "<li>" . $this->renderChildren() . "</li>\n";
    }
}

