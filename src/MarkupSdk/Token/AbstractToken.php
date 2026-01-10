<?php

namespace Hytmng\MarkupSdk\Token;

use Hytmng\MarkupSdk\Token\TokenInterface;

abstract class AbstractToken implements TokenInterface
{
    protected string $value;

    public function __construct()
    {
        $this->value = '';
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }
}
