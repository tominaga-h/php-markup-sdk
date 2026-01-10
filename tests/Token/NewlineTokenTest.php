<?php

declare(strict_types=1);

namespace Tests\Token;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Hytmng\MarkupSdk\Token\NewlineToken;

class NewlineTokenTest extends TestCase
{
    #[Test]
    public function patternMatchesNewlineAtBeginning(): void
    {
        $token = new NewlineToken();
        $this->assertSame('/^\R/', $token->getPattern());
    }

    #[Test]
    public function descriptionIsCorrect(): void
    {
        $token = new NewlineToken();
        $this->assertSame('改行(\n)のトークン', $token->getDescription());
    }

    #[Test]
    public function canSetAndGetValue(): void
    {
        $token = new NewlineToken();
        $token->setValue("\n");
        $this->assertSame("\n", $token->getValue());
    }

    #[Test]
    public function canSetCRLF(): void
    {
        $token = new NewlineToken();
        $token->setValue("\r\n");
        $this->assertSame("\r\n", $token->getValue());
    }

    #[Test]
    public function initialValueIsEmptyString(): void
    {
        $token = new NewlineToken();
        $this->assertSame('', $token->getValue());
    }
}
