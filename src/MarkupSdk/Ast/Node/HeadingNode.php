<?php

namespace Hytmng\MarkupSdk\Ast\Node;

use Hytmng\MarkupSdk\Ast\Node\BaseNode;

class HeadingNode extends BaseNode
{
    public function getType(): string
    {
        return 'heading';
    }

    public function getAttributeSchema(): array
    {
        return [
            'level' => 'integer', // 見出しレベル。 Level of the heading.
        ];
    }

    public function toHtml(): string {
        $level = $this->getAttribute('level') ?? 1;
        return "<h{$level}>" . $this->renderChildren() . "</h{$level}>\n";
    }
}
