<?php

declare(strict_types=1);

namespace Tests\Token;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Hytmng\MarkupSdk\Token\HashToken;

class HashTokenTest extends TestCase
{
    #[Test]
    public function patternMatchesHashAtBeginning(): void
    {
        $token = new HashToken();
        $this->assertSame('/^#/', $token->getPattern());
    }

    #[Test]
    public function canMatchHashAtBeginning(): void
    {
        $token = new HashToken();
        $this->assertSame(preg_match($token->getPattern(), '# hello'), 1);
        $this->assertSame(preg_match($token->getPattern(), 'hello #'), 0);
    }

    #[Test]
    public function descriptionIsCorrect(): void
    {
        $token = new HashToken();
        $this->assertSame('ハッシュ(#)記号のトークン', $token->getDescription());
    }

    #[Test]
    public function canSetAndGetValue(): void
    {
        $token = new HashToken();
        $token->setValue('#');
        $this->assertSame('#', $token->getValue());
    }

    #[Test]
    public function initialValueIsEmptyString(): void
    {
        $token = new HashToken();
        $this->assertSame('', $token->getValue());
    }
}
