<?php

declare(strict_types=1);

namespace Tests\Lexer;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Hytmng\MarkupSdk\Lexer\Lexer;
use Hytmng\MarkupSdk\Token\HashToken;
use Hytmng\MarkupSdk\Token\SpaceToken;
use Hytmng\MarkupSdk\Token\NewlineToken;
use Hytmng\MarkupSdk\Token\AsteriskToken;
use Hytmng\MarkupSdk\Token\TextToken;

class LexerTest extends TestCase
{
    #[Test]
    public function canRegisterSingleToken(): void
    {
        $lexer = new Lexer();
        $lexer->registerToken(new HashToken());

        $tokens = $lexer->tokenize('#');
        $this->assertCount(1, $tokens);
        $this->assertInstanceOf(HashToken::class, $tokens[0]);
        $this->assertSame('#', $tokens[0]->getValue());
    }

    #[Test]
    public function canRegisterMultipleTokensAtOnce(): void
    {
        $lexer = new Lexer();
        $lexer->registerTokens([
            new HashToken(),
            new SpaceToken(),
        ]);

        $tokens = $lexer->tokenize('# ');
        $this->assertCount(2, $tokens);
        $this->assertInstanceOf(HashToken::class, $tokens[0]);
        $this->assertInstanceOf(SpaceToken::class, $tokens[1]);
    }

    #[Test]
    public function throwsExceptionWhenRegisteringNonTokenInterface(): void
    {
        $lexer = new Lexer();

        $this->expectException(\InvalidArgumentException::class);
        $lexer->registerTokens(['invalid']);
    }

    #[Test]
    public function nonSymbolCharactersAreAggregatedIntoTextToken(): void
    {
        $lexer = new Lexer();
        $lexer->registerToken(new HashToken());

        $tokens = $lexer->tokenize('Hello');
        $this->assertCount(1, $tokens);
        $this->assertInstanceOf(TextToken::class, $tokens[0]);
        $this->assertSame('Hello', $tokens[0]->getValue());
    }

    #[Test]
    public function consecutiveTextIsMergedIntoSingleTextToken(): void
    {
        $lexer = new Lexer();
        $lexer->registerTokens([
            new HashToken(),
            new SpaceToken(),
        ]);

        $tokens = $lexer->tokenize('abc');
        $this->assertCount(1, $tokens);
        $this->assertInstanceOf(TextToken::class, $tokens[0]);
        $this->assertSame('abc', $tokens[0]->getValue());
    }

    #[Test]
    public function canTokenizeMarkdownHeadingFormat(): void
    {
        $lexer = new Lexer();
        $lexer->registerTokens([
            new HashToken(),
            new SpaceToken(),
            new NewlineToken(),
        ]);

        $tokens = $lexer->tokenize("# Hello\n");

        $this->assertCount(4, $tokens);
        $this->assertInstanceOf(HashToken::class, $tokens[0]);
        $this->assertInstanceOf(SpaceToken::class, $tokens[1]);
        $this->assertInstanceOf(TextToken::class, $tokens[2]);
        $this->assertSame('Hello', $tokens[2]->getValue());
        $this->assertInstanceOf(NewlineToken::class, $tokens[3]);
    }

    #[Test]
    public function canTokenizeConsecutiveHashes(): void
    {
        $lexer = new Lexer();
        $lexer->registerToken(new HashToken());

        $tokens = $lexer->tokenize('###');

        $this->assertCount(3, $tokens);
        foreach ($tokens as $token) {
            $this->assertInstanceOf(HashToken::class, $token);
            $this->assertSame('#', $token->getValue());
        }
    }

    #[Test]
    public function tokenizingEmptyStringReturnsEmptyArray(): void
    {
        $lexer = new Lexer();
        $lexer->registerToken(new HashToken());

        $tokens = $lexer->tokenize('');
        $this->assertCount(0, $tokens);
    }

    #[Test]
    public function canTokenizeAsterisks(): void
    {
        $lexer = new Lexer();
        $lexer->registerToken(new AsteriskToken());

        $tokens = $lexer->tokenize('**bold**');

        $this->assertCount(5, $tokens);
        $this->assertInstanceOf(AsteriskToken::class, $tokens[0]);
        $this->assertInstanceOf(AsteriskToken::class, $tokens[1]);
        $this->assertInstanceOf(TextToken::class, $tokens[2]);
        $this->assertSame('bold', $tokens[2]->getValue());
        $this->assertInstanceOf(AsteriskToken::class, $tokens[3]);
        $this->assertInstanceOf(AsteriskToken::class, $tokens[4]);
    }

    #[Test]
    public function canTokenizeMultiLineText(): void
    {
        $lexer = new Lexer();
        $lexer->registerTokens([
            new HashToken(),
            new SpaceToken(),
            new NewlineToken(),
        ]);

        $tokens = $lexer->tokenize("# Title\nContent");

        $this->assertCount(5, $tokens);
        $this->assertInstanceOf(HashToken::class, $tokens[0]);
        $this->assertInstanceOf(SpaceToken::class, $tokens[1]);
        $this->assertInstanceOf(TextToken::class, $tokens[2]);
        $this->assertSame('Title', $tokens[2]->getValue());
        $this->assertInstanceOf(NewlineToken::class, $tokens[3]);
        $this->assertInstanceOf(TextToken::class, $tokens[4]);
        $this->assertSame('Content', $tokens[4]->getValue());
    }
}
