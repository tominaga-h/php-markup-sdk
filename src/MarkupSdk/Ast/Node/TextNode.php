<?php

namespace Hytmng\MarkupSdk\Ast\Node;

use Hytmng\MarkupSdk\Ast\Node\BaseNode;

class TextNode extends BaseNode
{
    public function getType(): string
    {
        return 'text';
    }
}
