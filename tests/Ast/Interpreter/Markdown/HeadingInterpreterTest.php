<?php

declare(strict_types=1);

namespace Tests\Ast\Interpreter\Markdown;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Hytmng\MarkupSdk\Parser\Parser;
use Hytmng\MarkupSdk\Parser\ParserContext;
use Hytmng\MarkupSdk\Parser\TokenStream;
use Hytmng\MarkupSdk\Token\HashToken;
use Hytmng\MarkupSdk\Token\SpaceToken;
use Hytmng\MarkupSdk\Token\TextToken;
use Hytmng\MarkupSdk\Token\NewlineToken;
use Hytmng\MarkupSdk\Ast\Node\Markdown\HeadingNode;
use Hytmng\MarkupSdk\Ast\Interpreter\Markdown\HeadingInterpreter;
use Hytmng\MarkupSdk\Ast\Interpreter\TextInterpreter;

class HeadingInterpreterTest extends TestCase
{
    private function createHashToken(): HashToken
    {
        $token = new HashToken();
        $token->setValue('#');
        return $token;
    }

    private function createSpaceToken(): SpaceToken
    {
        $token = new SpaceToken();
        $token->setValue(' ');
        return $token;
    }

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
    public function priorityIs100(): void
    {
        $interpreter = new HeadingInterpreter();
        $this->assertSame(100, $interpreter->getPriority());
    }

    #[Test]
    public function canInterpretReturnsTrueWhenHashFollowedBySpace(): void
    {
        $interpreter = new HeadingInterpreter();

        $tokens = [
            $this->createHashToken(),
            $this->createSpaceToken(),
            $this->createTextToken('Title'),
        ];
        $stream = new TokenStream($tokens);

        $this->assertTrue($interpreter->canInterpret($stream));
    }

    #[Test]
    public function canInterpretReturnsTrueWhenMultipleHashesFollowedBySpace(): void
    {
        $interpreter = new HeadingInterpreter();

        $tokens = [
            $this->createHashToken(),
            $this->createHashToken(),
            $this->createHashToken(),
            $this->createSpaceToken(),
            $this->createTextToken('H3 Title'),
        ];
        $stream = new TokenStream($tokens);

        $this->assertTrue($interpreter->canInterpret($stream));
    }

    #[Test]
    public function canInterpretReturnsFalseWhenNoSpaceAfterHash(): void
    {
        $interpreter = new HeadingInterpreter();

        $tokens = [
            $this->createHashToken(),
            $this->createTextToken('NoSpace'),
        ];
        $stream = new TokenStream($tokens);

        $this->assertFalse($interpreter->canInterpret($stream));
    }

    #[Test]
    public function canInterpretReturnsFalseWhenNotStartingWithHash(): void
    {
        $interpreter = new HeadingInterpreter();

        $tokens = [
            $this->createTextToken('Not a heading'),
        ];
        $stream = new TokenStream($tokens);

        $this->assertFalse($interpreter->canInterpret($stream));
    }

    #[Test]
    public function canParseH1Heading(): void
    {
        $parser = new Parser();
        $parser->registerInterpreters([
            new HeadingInterpreter(),
            new TextInterpreter(),
        ]);

        $tokens = [
            $this->createHashToken(),
            $this->createSpaceToken(),
            $this->createTextToken('Title'),
        ];
        $stream = new TokenStream($tokens);
        $context = new ParserContext($parser, $stream);

        $interpreter = new HeadingInterpreter();
        $node = $interpreter->interpret($stream, $context);

        $this->assertInstanceOf(HeadingNode::class, $node);
        $this->assertSame(1, $node->getAttribute('level'));
    }

    #[Test]
    public function canParseH3Heading(): void
    {
        $parser = new Parser();
        $parser->registerInterpreters([
            new HeadingInterpreter(),
            new TextInterpreter(),
        ]);

        $tokens = [
            $this->createHashToken(),
            $this->createHashToken(),
            $this->createHashToken(),
            $this->createSpaceToken(),
            $this->createTextToken('H3 Title'),
        ];
        $stream = new TokenStream($tokens);
        $context = new ParserContext($parser, $stream);

        $interpreter = new HeadingInterpreter();
        $node = $interpreter->interpret($stream, $context);

        $this->assertInstanceOf(HeadingNode::class, $node);
        $this->assertSame(3, $node->getAttribute('level'));
    }

    #[Test]
    public function headingContainsTextAsChild(): void
    {
        $parser = new Parser();
        $parser->registerInterpreters([
            new HeadingInterpreter(),
            new TextInterpreter(),
        ]);

        $tokens = [
            $this->createHashToken(),
            $this->createSpaceToken(),
            $this->createTextToken('My Title'),
        ];
        $stream = new TokenStream($tokens);
        $context = new ParserContext($parser, $stream);

        $interpreter = new HeadingInterpreter();
        $node = $interpreter->interpret($stream, $context);

        $children = $node->getChildren();
        $this->assertCount(1, $children);
        $this->assertSame('text', $children[0]->getType());
        $this->assertSame('My Title', $children[0]->getAttribute('content'));
    }

    #[Test]
    public function headingParsingStopsAtNewline(): void
    {
        $parser = new Parser();
        $parser->registerInterpreters([
            new HeadingInterpreter(),
            new TextInterpreter(),
        ]);

        $tokens = [
            $this->createHashToken(),
            $this->createSpaceToken(),
            $this->createTextToken('Title'),
            $this->createNewlineToken(),
            $this->createTextToken('Next line'),
        ];
        $stream = new TokenStream($tokens);
        $context = new ParserContext($parser, $stream);

        $interpreter = new HeadingInterpreter();
        $node = $interpreter->interpret($stream, $context);

        // Heading children should only contain "Title"
        $children = $node->getChildren();
        $this->assertCount(1, $children);
        $this->assertSame('Title', $children[0]->getAttribute('content'));

        // Stream should be at "Next line" position
        $this->assertInstanceOf(TextToken::class, $stream->current());
        $this->assertSame('Next line', $stream->current()->getValue());
    }
}

