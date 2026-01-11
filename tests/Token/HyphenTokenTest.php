<?php

declare(strict_types=1);

namespace Tests\Token;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Hytmng\MarkupSdk\Token\HyphenToken;

class HyphenTokenTest extends TestCase
{
    #[Test]
    public function patternMatchesHyphenAtBeginning(): void
    {
        $token = new HyphenToken();
        $this->assertSame('/^-/', $token->getPattern());
    }

    #[Test]
    public function descriptionIsCorrect(): void
    {
        $token = new HyphenToken();
        $this->assertSame('ハイフン(-)記号のトークン', $token->getDescription());
    }

    #[Test]
    public function canSetAndGetValue(): void
    {
        $token = new HyphenToken();
        $token->setValue('-');
        $this->assertSame('-', $token->getValue());
    }

    #[Test]
    public function initialValueIsEmptyString(): void
    {
        $token = new HyphenToken();
        $this->assertSame('', $token->getValue());
    }
}

