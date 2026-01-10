<?php

declare(strict_types=1);

namespace Tests\Token;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Hytmng\MarkupSdk\Token\AsteriskToken;

class AsteriskTokenTest extends TestCase
{
    #[Test]
    public function patternMatchesAsteriskAtBeginning(): void
    {
        $token = new AsteriskToken();
        $this->assertSame('/^\*/', $token->getPattern());
    }

    #[Test]
    public function descriptionIsCorrect(): void
    {
        $token = new AsteriskToken();
        $this->assertSame('アスタリスク(*)記号のトークン', $token->getDescription());
    }

    #[Test]
    public function canSetAndGetValue(): void
    {
        $token = new AsteriskToken();
        $token->setValue('*');
        $this->assertSame('*', $token->getValue());
    }

    #[Test]
    public function initialValueIsEmptyString(): void
    {
        $token = new AsteriskToken();
        $this->assertSame('', $token->getValue());
    }
}
