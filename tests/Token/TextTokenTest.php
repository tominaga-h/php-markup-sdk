<?php

declare(strict_types=1);

namespace Tests\Token;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Hytmng\MarkupSdk\Token\TextToken;

class TextTokenTest extends TestCase
{
    #[Test]
    public function patternIsEmptyString(): void
    {
        $token = new TextToken();
        $this->assertSame('', $token->getPattern());
    }

    #[Test]
    public function descriptionIsCorrect(): void
    {
        $token = new TextToken();
        $this->assertSame('どのトークンにも当てはまらない文字列のトークン', $token->getDescription());
    }

    #[Test]
    public function canSetAndGetValue(): void
    {
        $token = new TextToken();
        $token->setValue('Hello World');
        $this->assertSame('Hello World', $token->getValue());
    }

    #[Test]
    public function canSetJapaneseValue(): void
    {
        $token = new TextToken();
        $token->setValue('こんにちは');
        $this->assertSame('こんにちは', $token->getValue());
    }

    #[Test]
    public function initialValueIsEmptyString(): void
    {
        $token = new TextToken();
        $this->assertSame('', $token->getValue());
    }
}
