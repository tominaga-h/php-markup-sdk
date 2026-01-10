<?php

declare(strict_types=1);

namespace Tests\Parser;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Hytmng\MarkupSdk\Parser\Parser;
use Hytmng\MarkupSdk\Parser\TokenStream;
use Hytmng\MarkupSdk\Token\HashToken;
use Hytmng\MarkupSdk\Token\SpaceToken;
use Hytmng\MarkupSdk\Token\TextToken;
use Hytmng\MarkupSdk\Token\NewlineToken;
use Hytmng\MarkupSdk\Ast\Node\DocumentNode;
use Hytmng\MarkupSdk\Ast\Node\HeadingNode;
use Hytmng\MarkupSdk\Ast\Node\TextNode;
use Hytmng\MarkupSdk\Ast\Interpreter\HeadingInterpreter;
use Hytmng\MarkupSdk\Ast\Interpreter\TextInterpreter;

class ParserTest extends TestCase
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
    public function canRegisterInterpreter(): void
    {
        $parser = new Parser();
        $parser->registerInterpreter(new TextInterpreter());

        $tokens = [$this->createTextToken('Hello')];
        $stream = new TokenStream($tokens);

        $result = $parser->parse($stream);

        $this->assertInstanceOf(DocumentNode::class, $result);
    }

    #[Test]
    public function canRegisterMultipleInterpretersAtOnce(): void
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

        $result = $parser->parse($stream);

        $this->assertInstanceOf(DocumentNode::class, $result);
        $this->assertCount(1, $result->getChildren());
        $this->assertInstanceOf(HeadingNode::class, $result->getChildren()[0]);
    }

    #[Test]
    public function throwsExceptionWhenRegisteringNonInterpreterInterface(): void
    {
        $parser = new Parser();

        $this->expectException(\InvalidArgumentException::class);
        $parser->registerInterpreters(['invalid']);
    }

    #[Test]
    public function interpretersAreSortedByPriority(): void
    {
        $parser = new Parser();

        // Register TextInterpreter (priority 0) first
        $parser->registerInterpreter(new TextInterpreter());
        // Register HeadingInterpreter (priority 100) later
        $parser->registerInterpreter(new HeadingInterpreter());

        // Heading-format tokens
        $tokens = [
            $this->createHashToken(),
            $this->createSpaceToken(),
            $this->createTextToken('Title'),
        ];
        $stream = new TokenStream($tokens);

        $result = $parser->parse($stream);

        // HeadingInterpreter with higher priority processes first
        $children = $result->getChildren();
        $this->assertCount(1, $children);
        $this->assertInstanceOf(HeadingNode::class, $children[0]);
    }

    #[Test]
    public function parseReturnsDocumentNode(): void
    {
        $parser = new Parser();
        $parser->registerInterpreter(new TextInterpreter());

        $tokens = [$this->createTextToken('Hello')];
        $stream = new TokenStream($tokens);

        $result = $parser->parse($stream);

        $this->assertInstanceOf(DocumentNode::class, $result);
        $this->assertSame('document', $result->getType());
    }

    #[Test]
    public function parseReturnsEmptyDocumentNodeForEmptyStream(): void
    {
        $parser = new Parser();
        $parser->registerInterpreter(new TextInterpreter());

        $stream = new TokenStream([]);

        $result = $parser->parse($stream);

        $this->assertInstanceOf(DocumentNode::class, $result);
        $this->assertCount(0, $result->getChildren());
    }

    #[Test]
    public function unmatchedTokensAreSkipped(): void
    {
        $parser = new Parser();
        // Only register HeadingInterpreter (does not match TextToken)
        $parser->registerInterpreter(new HeadingInterpreter());

        $tokens = [$this->createTextToken('Plain text')];
        $stream = new TokenStream($tokens);

        $result = $parser->parse($stream);

        // Unmatched tokens are skipped, children is empty
        $this->assertInstanceOf(DocumentNode::class, $result);
        $this->assertCount(0, $result->getChildren());
    }

    #[Test]
    public function canParseMultipleNodes(): void
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
            $this->createTextToken('Content'),
        ];
        $stream = new TokenStream($tokens);

        $result = $parser->parse($stream);

        $children = $result->getChildren();
        $this->assertCount(2, $children);
        $this->assertInstanceOf(HeadingNode::class, $children[0]);
        $this->assertInstanceOf(TextNode::class, $children[1]);
    }

    #[Test]
    public function parseStepParsesSingleNode(): void
    {
        $parser = new Parser();
        $parser->registerInterpreter(new TextInterpreter());

        $tokens = [
            $this->createTextToken('Hello'),
            $this->createNewlineToken(),
            $this->createTextToken('World'),
        ];
        $stream = new TokenStream($tokens);

        $context = new \Hytmng\MarkupSdk\Parser\ParserContext($parser, $stream);
        $node = $parser->parseStep($stream, $context);

        $this->assertInstanceOf(TextNode::class, $node);
        $this->assertSame('Hello', $node->getAttribute('content'));
    }
}
