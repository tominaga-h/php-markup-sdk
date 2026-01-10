<?php

declare(strict_types=1);

namespace Tests\Ast\Interpreter;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Hytmng\MarkupSdk\Parser\Parser;
use Hytmng\MarkupSdk\Parser\ParserContext;
use Hytmng\MarkupSdk\Parser\TokenStream;
use Hytmng\MarkupSdk\Token\TextToken;
use Hytmng\MarkupSdk\Token\SpaceToken;
use Hytmng\MarkupSdk\Token\HashToken;
use Hytmng\MarkupSdk\Token\NewlineToken;
use Hytmng\MarkupSdk\Ast\Node\TextNode;
use Hytmng\MarkupSdk\Ast\Interpreter\TextInterpreter;

class TextInterpreterTest extends TestCase
{
    private function createTextToken(string $value): TextToken
    {
        $token = new TextToken();
        $token->setValue($value);
        return $token;
    }

    private function createSpaceToken(): SpaceToken
    {
        $token = new SpaceToken();
        $token->setValue(' ');
        return $token;
    }

    private function createHashToken(): HashToken
    {
        $token = new HashToken();
        $token->setValue('#');
        return $token;
    }

    private function createNewlineToken(): NewlineToken
    {
        $token = new NewlineToken();
        $token->setValue("\n");
        return $token;
    }

    #[Test]
    public function priorityIs0(): void
    {
        $interpreter = new TextInterpreter();
        $this->assertSame(0, $interpreter->getPriority());
    }

    #[Test]
    public function canInterpretReturnsTrueForTextToken(): void
    {
        $interpreter = new TextInterpreter();

        $tokens = [$this->createTextToken('Hello')];
        $stream = new TokenStream($tokens);

        $this->assertTrue($interpreter->canInterpret($stream));
    }

    #[Test]
    public function canInterpretReturnsTrueForSpaceToken(): void
    {
        $interpreter = new TextInterpreter();

        $tokens = [$this->createSpaceToken()];
        $stream = new TokenStream($tokens);

        $this->assertTrue($interpreter->canInterpret($stream));
    }

    #[Test]
    public function canInterpretReturnsFalseForHashToken(): void
    {
        $interpreter = new TextInterpreter();

        $tokens = [$this->createHashToken()];
        $stream = new TokenStream($tokens);

        $this->assertFalse($interpreter->canInterpret($stream));
    }

    #[Test]
    public function canInterpretReturnsFalseForNewlineToken(): void
    {
        $interpreter = new TextInterpreter();

        $tokens = [$this->createNewlineToken()];
        $stream = new TokenStream($tokens);

        $this->assertFalse($interpreter->canInterpret($stream));
    }

    #[Test]
    public function canParseSingleTextToken(): void
    {
        $parser = new Parser();
        $parser->registerInterpreter(new TextInterpreter());

        $tokens = [$this->createTextToken('Hello')];
        $stream = new TokenStream($tokens);
        $context = new ParserContext($parser, $stream);

        $interpreter = new TextInterpreter();
        $node = $interpreter->interpret($stream, $context);

        $this->assertInstanceOf(TextNode::class, $node);
        $this->assertSame('Hello', $node->getAttribute('content'));
    }

    #[Test]
    public function consecutiveTextTokensAreConcatenated(): void
    {
        $parser = new Parser();
        $parser->registerInterpreter(new TextInterpreter());

        $tokens = [
            $this->createTextToken('Hello'),
            $this->createTextToken('World'),
        ];
        $stream = new TokenStream($tokens);
        $context = new ParserContext($parser, $stream);

        $interpreter = new TextInterpreter();
        $node = $interpreter->interpret($stream, $context);

        $this->assertInstanceOf(TextNode::class, $node);
        $this->assertSame('HelloWorld', $node->getAttribute('content'));
    }

    #[Test]
    public function textAndSpaceTokensAreConcatenated(): void
    {
        $parser = new Parser();
        $parser->registerInterpreter(new TextInterpreter());

        $tokens = [
            $this->createTextToken('Hello'),
            $this->createSpaceToken(),
            $this->createTextToken('World'),
        ];
        $stream = new TokenStream($tokens);
        $context = new ParserContext($parser, $stream);

        $interpreter = new TextInterpreter();
        $node = $interpreter->interpret($stream, $context);

        $this->assertInstanceOf(TextNode::class, $node);
        $this->assertSame('Hello World', $node->getAttribute('content'));
    }

    #[Test]
    public function concatenationStopsAtHashToken(): void
    {
        $parser = new Parser();
        $parser->registerInterpreter(new TextInterpreter());

        $tokens = [
            $this->createTextToken('Before'),
            $this->createHashToken(),
            $this->createTextToken('After'),
        ];
        $stream = new TokenStream($tokens);
        $context = new ParserContext($parser, $stream);

        $interpreter = new TextInterpreter();
        $node = $interpreter->interpret($stream, $context);

        $this->assertInstanceOf(TextNode::class, $node);
        $this->assertSame('Before', $node->getAttribute('content'));

        // Stream should be at HashToken position
        $this->assertInstanceOf(HashToken::class, $stream->current());
    }

    #[Test]
    public function concatenationStopsAtNewlineToken(): void
    {
        $parser = new Parser();
        $parser->registerInterpreter(new TextInterpreter());

        $tokens = [
            $this->createTextToken('Line1'),
            $this->createNewlineToken(),
            $this->createTextToken('Line2'),
        ];
        $stream = new TokenStream($tokens);
        $context = new ParserContext($parser, $stream);

        $interpreter = new TextInterpreter();
        $node = $interpreter->interpret($stream, $context);

        $this->assertInstanceOf(TextNode::class, $node);
        $this->assertSame('Line1', $node->getAttribute('content'));

        // Stream should be at NewlineToken position
        $this->assertInstanceOf(NewlineToken::class, $stream->current());
    }

    #[Test]
    public function multipleSpacesAreConcatenated(): void
    {
        $parser = new Parser();
        $parser->registerInterpreter(new TextInterpreter());

        $tokens = [
            $this->createSpaceToken(),
            $this->createSpaceToken(),
            $this->createSpaceToken(),
        ];
        $stream = new TokenStream($tokens);
        $context = new ParserContext($parser, $stream);

        $interpreter = new TextInterpreter();
        $node = $interpreter->interpret($stream, $context);

        $this->assertInstanceOf(TextNode::class, $node);
        $this->assertSame('   ', $node->getAttribute('content'));
    }
}
