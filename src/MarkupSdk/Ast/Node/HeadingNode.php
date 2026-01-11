<?php

namespace Hytmng\MarkupSdk\Ast\Node;

use Hytmng\MarkupSdk\Ast\Node\BaseNode;

class HeadingNode extends BaseNode
{
    public function getType(): string
    {
        return 'heading';
    }

    public function toHtml(): string
    {
        $level = $this->getAttribute('level') ?? 1;
        $content = "";

        foreach ($this->children as $child) {
            $content .= $child->toHtml();
        }

        return "<h{$level}>{$content}</h{$level}>\n";
    }
}
