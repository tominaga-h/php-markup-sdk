<?php

namespace Hytmng\MarkupSdk\Token;

use Hytmng\MarkupSdk\Token\AbstractToken;

class HyphenToken extends AbstractToken
{

    public function getDescription(): string
    {
        return 'ハイフン(-)記号のトークン';
    }

    public function getPattern(): string
    {
        return '/^-/';
    }

}

