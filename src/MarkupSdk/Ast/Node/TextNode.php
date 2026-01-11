<?php

namespace Hytmng\MarkupSdk\Ast\Node;

use Hytmng\MarkupSdk\Ast\Node\BaseNode;

class TextNode extends BaseNode
{
    public function getType(): string
    {
        return 'text';
    }

    public function toHtml(): string
    {
        // エスケープして返す。 Return escaped HTML.
        return htmlspecialchars($this->getAttribute('content') ?? '', ENT_QUOTES, 'UTF-8');
    }
}
