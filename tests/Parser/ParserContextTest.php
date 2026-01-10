<?php

declare(strict_types=1);

namespace Tests\Parser;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Hytmng\MarkupSdk\Parser\Parser;
use Hytmng\MarkupSdk\Parser\ParserContext;
use Hytmng\MarkupSdk\Parser\TokenStream;
use Hytmng\MarkupSdk\Token\TextToken;
use Hytmng\MarkupSdk\Token\NewlineToken;
use Hytmng\MarkupSdk\Ast\Interpreter\TextInterpreter;

class ParserContextTest extends TestCase
{
    private function createTextToken(string $value): TextToken
    {
        $token = new TextToken();
        $token->setValue($value);
        return $token;
    }

    private function createNewlineToken(): NewlineToken
    {
        $token = new NewlineToken();
        $token->setValue("\n");
        return $token;
    }

    #[Test]
    public function parseNextReturnsSingleNode(): void
    {
        $parser = new Parser();
        $parser->registerInterpreter(new TextInterpreter());

        $tokens = [$this->createTextToken('Hello')];
        $stream = new TokenStream($tokens);
        $context = new ParserContext($parser, $stream);

        $node = $context->parseNext();

        $this->assertNotNull($node);
        $this->assertSame('text', $node->getType());
        $this->assertSame('Hello', $node->getAttribute('content'));
    }

    #[Test]
    public function parseNextReturnsNullWhenNoInterpreterMatches(): void
    {
        $parser = new Parser();
        // No interpreter registered

        $tokens = [$this->createTextToken('Hello')];
        $stream = new TokenStream($tokens);
        $context = new ParserContext($parser, $stream);

        $node = $context->parseNext();

        $this->assertNull($node);
    }

    #[Test]
    public function parseUntilCollectsNodesUntilConditionMet(): void
    {
        $parser = new Parser();
        $parser->registerInterpreter(new TextInterpreter());

        $tokens = [
            $this->createTextToken('Hello'),
            $this->createTextToken('World'),
            $this->createNewlineToken(),
            $this->createTextToken('End'),
        ];
        $stream = new TokenStream($tokens);
        $context = new ParserContext($parser, $stream);

        // Stop when newline token is encountered
        $nodes = $context->parseUntil(function (TokenStream $s) {
            return $s->current() instanceof NewlineToken;
        });

        // TextInterpreter merges consecutive text tokens,
        // so "Hello" and "World" become one node
        $this->assertCount(1, $nodes);
        $this->assertSame('HelloWorld', $nodes[0]->getAttribute('content'));
    }

    #[Test]
    public function parseUntilParsesToEndOfStream(): void
    {
        $parser = new Parser();
        $parser->registerInterpreter(new TextInterpreter());

        $tokens = [
            $this->createTextToken('Hello'),
            $this->createTextToken('World'),
        ];
        $stream = new TokenStream($tokens);
        $context = new ParserContext($parser, $stream);

        // Condition that is never satisfied
        $nodes = $context->parseUntil(function (TokenStream $s) {
            return false;
        });

        $this->assertCount(1, $nodes);
        $this->assertSame('HelloWorld', $nodes[0]->getAttribute('content'));
    }

    #[Test]
    public function parseUntilReturnsEmptyArrayForEmptyStream(): void
    {
        $parser = new Parser();
        $parser->registerInterpreter(new TextInterpreter());

        $stream = new TokenStream([]);
        $context = new ParserContext($parser, $stream);

        $nodes = $context->parseUntil(function (TokenStream $s) {
            return false;
        });

        $this->assertCount(0, $nodes);
    }
}
