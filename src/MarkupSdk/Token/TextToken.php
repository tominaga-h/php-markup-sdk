<?php

namespace Hytmng\MarkupSdk\Token;

use Hytmng\MarkupSdk\Token\AbstractToken;

class TextToken extends AbstractToken
{

    public function getDescription(): string
    {
        return 'どのトークンにも当てはまらない文字列のトークン';
    }

    public function getPattern(): string
    {
        return ''; // 特殊なトークンのためパターンは不要
    }

}
