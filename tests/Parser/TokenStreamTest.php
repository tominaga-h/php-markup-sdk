<?php

declare(strict_types=1);

namespace Tests\Parser;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Hytmng\MarkupSdk\Parser\TokenStream;
use Hytmng\MarkupSdk\Token\HashToken;
use Hytmng\MarkupSdk\Token\SpaceToken;
use Hytmng\MarkupSdk\Token\TextToken;

class TokenStreamTest extends TestCase
{
    private function createTextToken(string $value): TextToken
    {
        $token = new TextToken();
        $token->setValue($value);
        return $token;
    }

    private function createHashToken(): HashToken
    {
        $token = new HashToken();
        $token->setValue('#');
        return $token;
    }

    #[Test]
    public function canGetCurrentToken(): void
    {
        $tokens = [$this->createHashToken()];
        $stream = new TokenStream($tokens);

        $this->assertInstanceOf(HashToken::class, $stream->current());
    }

    #[Test]
    public function currentReturnsNullForEmptyStream(): void
    {
        $stream = new TokenStream([]);

        $this->assertNull($stream->current());
    }

    #[Test]
    public function canPeekNextToken(): void
    {
        $tokens = [
            $this->createHashToken(),
            $this->createTextToken('Hello'),
        ];
        $stream = new TokenStream($tokens);

        $this->assertInstanceOf(TextToken::class, $stream->peek());
        // current should remain unchanged
        $this->assertInstanceOf(HashToken::class, $stream->current());
    }

    #[Test]
    public function canPeekWithOffset(): void
    {
        $space = new SpaceToken();
        $space->setValue(' ');

        $tokens = [
            $this->createHashToken(),
            $space,
            $this->createTextToken('Hello'),
        ];
        $stream = new TokenStream($tokens);

        $this->assertInstanceOf(SpaceToken::class, $stream->peek(1));
        $this->assertInstanceOf(TextToken::class, $stream->peek(2));
    }

    #[Test]
    public function peekReturnsNullForOutOfBounds(): void
    {
        $tokens = [$this->createHashToken()];
        $stream = new TokenStream($tokens);

        $this->assertNull($stream->peek(1));
        $this->assertNull($stream->peek(100));
    }

    #[Test]
    public function canAdvanceCursorWithNext(): void
    {
        $tokens = [
            $this->createHashToken(),
            $this->createTextToken('Hello'),
        ];
        $stream = new TokenStream($tokens);

        $this->assertInstanceOf(HashToken::class, $stream->current());
        $stream->next();
        $this->assertInstanceOf(TextToken::class, $stream->current());
    }

    #[Test]
    public function canDetectEndOfStream(): void
    {
        $tokens = [$this->createHashToken()];
        $stream = new TokenStream($tokens);

        $this->assertFalse($stream->isEnd());
        $stream->next();
        $this->assertTrue($stream->isEnd());
    }

    #[Test]
    public function emptyStreamIsAtEndInitially(): void
    {
        $stream = new TokenStream([]);

        $this->assertTrue($stream->isEnd());
    }

    #[Test]
    public function canIterateThroughMultipleTokens(): void
    {
        $space = new SpaceToken();
        $space->setValue(' ');

        $tokens = [
            $this->createHashToken(),
            $space,
            $this->createTextToken('Test'),
        ];
        $stream = new TokenStream($tokens);

        $collected = [];
        while (!$stream->isEnd()) {
            $collected[] = $stream->current();
            $stream->next();
        }

        $this->assertCount(3, $collected);
        $this->assertInstanceOf(HashToken::class, $collected[0]);
        $this->assertInstanceOf(SpaceToken::class, $collected[1]);
        $this->assertInstanceOf(TextToken::class, $collected[2]);
    }
}
