<?php

namespace Hytmng\MarkupSdk\Token;

use Hytmng\MarkupSdk\Token\AbstractToken;

class NewlineToken extends AbstractToken
{

    public function getDescription(): string
    {
        return '改行(\n)のトークン';
    }

    public function getPattern(): string
    {
        return '/^\n/';
    }

}
