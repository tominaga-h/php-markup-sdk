<?php

namespace Hytmng\MarkupSdk\Token;

use Hytmng\MarkupSdk\Token\AbstractToken;

class SpaceToken extends AbstractToken
{

    public function getDescription(): string
    {
        return '半角スペース( )のトークン';
    }

    public static function getPattern(): string
    {
        return '/^ /';
    }

}
