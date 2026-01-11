<?php

declare(strict_types=1);

namespace Tests\Ast\Interpreter\Markdown;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Hytmng\MarkupSdk\Parser\Parser;
use Hytmng\MarkupSdk\Parser\ParserContext;
use Hytmng\MarkupSdk\Parser\TokenStream;
use Hytmng\MarkupSdk\Token\AsteriskToken;
use Hytmng\MarkupSdk\Token\TextToken;
use Hytmng\MarkupSdk\Token\SpaceToken;
use Hytmng\MarkupSdk\Ast\Node\Markdown\BoldNode;
use Hytmng\MarkupSdk\Ast\Interpreter\Markdown\BoldInterpreter;
use Hytmng\MarkupSdk\Ast\Interpreter\TextInterpreter;

class BoldInterpreterTest extends TestCase
{
    private function createAsteriskToken(): AsteriskToken
    {
        $token = new AsteriskToken();
        $token->setValue('*');
        return $token;
    }

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

    #[Test]
    public function priorityIs50(): void
    {
        $interpreter = new BoldInterpreter();
        $this->assertSame(50, $interpreter->getPriority());
    }

    #[Test]
    public function canInterpretReturnsTrueWhenDoubleAsteriskWithClosing(): void
    {
        $interpreter = new BoldInterpreter();

        // **bold**
        $tokens = [
            $this->createAsteriskToken(),
            $this->createAsteriskToken(),
            $this->createTextToken('bold'),
            $this->createAsteriskToken(),
            $this->createAsteriskToken(),
        ];
        $stream = new TokenStream($tokens);

        $this->assertTrue($interpreter->canInterpret($stream));
    }

    #[Test]
    public function canInterpretReturnsFalseWhenSingleAsterisk(): void
    {
        $interpreter = new BoldInterpreter();

        // *italic* (not bold)
        $tokens = [
            $this->createAsteriskToken(),
            $this->createTextToken('italic'),
            $this->createAsteriskToken(),
        ];
        $stream = new TokenStream($tokens);

        $this->assertFalse($interpreter->canInterpret($stream));
    }

    #[Test]
    public function canInterpretReturnsFalseWhenNoClosingDelimiter(): void
    {
        $interpreter = new BoldInterpreter();

        // **unclosed
        $tokens = [
            $this->createAsteriskToken(),
            $this->createAsteriskToken(),
            $this->createTextToken('unclosed'),
        ];
        $stream = new TokenStream($tokens);

        $this->assertFalse($interpreter->canInterpret($stream));
    }

    #[Test]
    public function canInterpretReturnsFalseWhenNotStartingWithAsterisk(): void
    {
        $interpreter = new BoldInterpreter();

        $tokens = [
            $this->createTextToken('Not bold'),
        ];
        $stream = new TokenStream($tokens);

        $this->assertFalse($interpreter->canInterpret($stream));
    }

    #[Test]
    public function canParseBoldText(): void
    {
        $parser = new Parser();
        $parser->registerInterpreters([
            new BoldInterpreter(),
            new TextInterpreter(),
        ]);

        // **bold**
        $tokens = [
            $this->createAsteriskToken(),
            $this->createAsteriskToken(),
            $this->createTextToken('bold'),
            $this->createAsteriskToken(),
            $this->createAsteriskToken(),
        ];
        $stream = new TokenStream($tokens);
        $context = new ParserContext($parser, $stream);

        $interpreter = new BoldInterpreter();
        $node = $interpreter->interpret($stream, $context);

        $this->assertInstanceOf(BoldNode::class, $node);
    }

    #[Test]
    public function boldContainsTextAsChild(): void
    {
        $parser = new Parser();
        $parser->registerInterpreters([
            new BoldInterpreter(),
            new TextInterpreter(),
        ]);

        // **bold text**
        $tokens = [
            $this->createAsteriskToken(),
            $this->createAsteriskToken(),
            $this->createTextToken('bold text'),
            $this->createAsteriskToken(),
            $this->createAsteriskToken(),
        ];
        $stream = new TokenStream($tokens);
        $context = new ParserContext($parser, $stream);

        $interpreter = new BoldInterpreter();
        $node = $interpreter->interpret($stream, $context);

        $children = $node->getChildren();
        $this->assertCount(1, $children);
        $this->assertSame('text', $children[0]->getType());
        $this->assertSame('bold text', $children[0]->getAttribute('content'));
    }

    #[Test]
    public function boldWithSpaceInContent(): void
    {
        $parser = new Parser();
        $parser->registerInterpreters([
            new BoldInterpreter(),
            new TextInterpreter(),
        ]);

        // **hello world**
        $tokens = [
            $this->createAsteriskToken(),
            $this->createAsteriskToken(),
            $this->createTextToken('hello'),
            $this->createSpaceToken(),
            $this->createTextToken('world'),
            $this->createAsteriskToken(),
            $this->createAsteriskToken(),
        ];
        $stream = new TokenStream($tokens);
        $context = new ParserContext($parser, $stream);

        $interpreter = new BoldInterpreter();
        $node = $interpreter->interpret($stream, $context);

        $children = $node->getChildren();
        $this->assertCount(1, $children);
        $this->assertSame('hello world', $children[0]->getAttribute('content'));
    }

    #[Test]
    public function streamPositionAfterBoldParsing(): void
    {
        $parser = new Parser();
        $parser->registerInterpreters([
            new BoldInterpreter(),
            new TextInterpreter(),
        ]);

        // **bold** after
        $tokens = [
            $this->createAsteriskToken(),
            $this->createAsteriskToken(),
            $this->createTextToken('bold'),
            $this->createAsteriskToken(),
            $this->createAsteriskToken(),
            $this->createTextToken('after'),
        ];
        $stream = new TokenStream($tokens);
        $context = new ParserContext($parser, $stream);

        $interpreter = new BoldInterpreter();
        $interpreter->interpret($stream, $context);

        // Stream should be at "after" position
        $this->assertInstanceOf(TextToken::class, $stream->current());
        $this->assertSame('after', $stream->current()->getValue());
    }

    #[Test]
    public function toHtmlRendersCorrectly(): void
    {
        $parser = new Parser();
        $parser->registerInterpreters([
            new BoldInterpreter(),
            new TextInterpreter(),
        ]);

        // **bold**
        $tokens = [
            $this->createAsteriskToken(),
            $this->createAsteriskToken(),
            $this->createTextToken('bold'),
            $this->createAsteriskToken(),
            $this->createAsteriskToken(),
        ];
        $stream = new TokenStream($tokens);
        $context = new ParserContext($parser, $stream);

        $interpreter = new BoldInterpreter();
        $node = $interpreter->interpret($stream, $context);

        $this->assertSame('<strong>bold</strong>', $node->toHtml());
    }
}

