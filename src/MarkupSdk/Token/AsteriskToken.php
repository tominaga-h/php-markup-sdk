<?php

namespace Hytmng\MarkupSdk\Token;

use Hytmng\MarkupSdk\Token\AbstractToken;

class AsteriskToken extends AbstractToken
{

    public function getDescription(): string
    {
        return 'アスタリスク(*)記号のトークン';
    }

    public function getPattern(): string
    {
        return '/^\*/';
    }

}
