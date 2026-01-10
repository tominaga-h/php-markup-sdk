<?php

namespace Hytmng\MarkupSdk\Token;

use Hytmng\MarkupSdk\Token\AbstractToken;

class HashToken extends AbstractToken
{

    public function getDescription(): string
    {
        return 'ハッシュ(#)記号のトークン';
    }

    public function getPattern(): string
    {
        return '/^#/';
    }

}
