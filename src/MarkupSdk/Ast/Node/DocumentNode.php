<?php

namespace Hytmng\MarkupSdk\Ast\Node;

use Hytmng\MarkupSdk\Ast\Node\BaseNode;

/**
 * ASTのルート（頂点）を表すノード。解析された全てのノードを保持する。
 * The root node of the AST. It holds all the parsed nodes of the document.
 */
class DocumentNode extends BaseNode
{
    public function getType(): string
    {
        return "document";
    }
}
