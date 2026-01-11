<?php

namespace Hytmng\MarkupSdk\Ast\Node\Markdown;

use Hytmng\MarkupSdk\Ast\Node\BaseNode;

/**
 * 太字ノード
 * Bold node for **text** syntax
 */
class BoldNode extends BaseNode
{
    public function getType(): string
    {
        return 'bold';
    }

    public function getAttributeSchema(): array
    {
        return [];
    }

    public function toHtml(): string
    {
        return '<strong>' . $this->renderChildren() . '</strong>';
    }
}

