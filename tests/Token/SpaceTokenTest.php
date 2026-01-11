<?php

declare(strict_types=1);

namespace Tests\Token;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Hytmng\MarkupSdk\Token\SpaceToken;

class SpaceTokenTest extends TestCase
{
    #[Test]
    public function patternMatchesSpaceAtBeginning(): void
    {
        $token = new SpaceToken();
        $this->assertSame('/^ /', $token->getPattern());
    }

    #[Test]
    public function canMatchSpaceAtBeginning(): void
    {
        $token = new SpaceToken();
        $this->assertSame(preg_match($token->getPattern(), ' hello'), 1);
        $this->assertSame(preg_match($token->getPattern(), 'hello '), 0);
    }

    #[Test]
    public function descriptionIsCorrect(): void
    {
        $token = new SpaceToken();
        $this->assertSame('半角スペース( )のトークン', $token->getDescription());
    }

    #[Test]
    public function canSetAndGetValue(): void
    {
        $token = new SpaceToken();
        $token->setValue(' ');
        $this->assertSame(' ', $token->getValue());
    }

    #[Test]
    public function initialValueIsEmptyString(): void
    {
        $token = new SpaceToken();
        $this->assertSame('', $token->getValue());
    }
}
