<?php

namespace Hytmng\MarkupSdk\Ast\Node;

use Hytmng\MarkupSdk\Ast\Node\BaseNode;

class HeadingNode extends BaseNode
{
    public function getType(): string
    {
        return 'heading';
    }
}
