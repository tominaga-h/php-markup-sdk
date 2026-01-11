<?php

namespace Hytmng\MarkupSdk\Ast\Node\Markdown;

use Hytmng\MarkupSdk\Ast\Node\BaseNode;

class ListNode extends BaseNode
{
    public function getType(): string
    {
        return 'list';
    }

    public function getAttributeSchema(): array
    {
        return []; // 順序なしリストのみなので属性なし。No attributes for unordered list only.
    }

    public function toHtml(): string
    {
        return "<ul>\n" . $this->renderChildren() . "</ul>\n";
    }
}

