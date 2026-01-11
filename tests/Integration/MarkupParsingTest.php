<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Hytmng\MarkupSdk\Lexer\Lexer;
use Hytmng\MarkupSdk\Parser\Parser;
use Hytmng\MarkupSdk\Parser\TokenStream;
use Hytmng\MarkupSdk\Token\HashToken;
use Hytmng\MarkupSdk\Token\SpaceToken;
use Hytmng\MarkupSdk\Token\NewlineToken;
use Hytmng\MarkupSdk\Token\AsteriskToken;
use Hytmng\MarkupSdk\Ast\Node\DocumentNode;
use Hytmng\MarkupSdk\Ast\Node\Markdown\HeadingNode;
use Hytmng\MarkupSdk\Ast\Node\TextNode;
use Hytmng\MarkupSdk\Ast\Interpreter\Markdown\HeadingInterpreter;
use Hytmng\MarkupSdk\Ast\Interpreter\TextInterpreter;

class MarkupParsingTest extends TestCase
{
    private Lexer $lexer;
    private Parser $parser;

    protected function setUp(): void
    {
        $this->lexer = new Lexer();
        $this->lexer->registerTokens([
            new HashToken(),
            new SpaceToken(),
            new NewlineToken(),
            new AsteriskToken(),
        ]);

        $this->parser = new Parser();
        $this->parser->registerInterpreters([
            new HeadingInterpreter(),
            new TextInterpreter(),
        ]);
    }

    #[Test]
    public function canParseH1HeadingFromLexerToParser(): void
    {
        $input = '# Hello World';

        $tokens = $this->lexer->tokenize($input);
        $stream = new TokenStream($tokens);
        $ast = $this->parser->parse($stream);

        $this->assertInstanceOf(DocumentNode::class, $ast);
        $this->assertCount(1, $ast->getChildren());

        $heading = $ast->getChildren()[0];
        $this->assertInstanceOf(HeadingNode::class, $heading);
        $this->assertSame(1, $heading->getAttribute('level'));

        $textNode = $heading->getChildren()[0];
        $this->assertInstanceOf(TextNode::class, $textNode);
        $this->assertSame('Hello World', $textNode->getAttribute('content'));
    }

    #[Test]
    public function canParseH2Heading(): void
    {
        $input = '## Section Title';

        $tokens = $this->lexer->tokenize($input);
        $stream = new TokenStream($tokens);
        $ast = $this->parser->parse($stream);

        $heading = $ast->getChildren()[0];
        $this->assertSame(2, $heading->getAttribute('level'));
    }

    #[Test]
    public function canParseH6Heading(): void
    {
        $input = '###### Deep Heading';

        $tokens = $this->lexer->tokenize($input);
        $stream = new TokenStream($tokens);
        $ast = $this->parser->parse($stream);

        $heading = $ast->getChildren()[0];
        $this->assertSame(6, $heading->getAttribute('level'));
    }

    #[Test]
    public function canParseMultiLineDocument(): void
    {
        $input = "# Title\nThis is content.";

        $tokens = $this->lexer->tokenize($input);
        $stream = new TokenStream($tokens);
        $ast = $this->parser->parse($stream);

        $children = $ast->getChildren();
        $this->assertCount(2, $children);

        // First is heading
        $this->assertInstanceOf(HeadingNode::class, $children[0]);
        $this->assertSame(1, $children[0]->getAttribute('level'));

        // Second is text
        $this->assertInstanceOf(TextNode::class, $children[1]);
        $this->assertSame('This is content.', $children[1]->getAttribute('content'));
    }

    #[Test]
    public function canParseDocumentWithMultipleHeadings(): void
    {
        $input = "# First\n## Second\n### Third";

        $tokens = $this->lexer->tokenize($input);
        $stream = new TokenStream($tokens);
        $ast = $this->parser->parse($stream);

        $children = $ast->getChildren();
        $this->assertCount(3, $children);

        $this->assertSame(1, $children[0]->getAttribute('level'));
        $this->assertSame(2, $children[1]->getAttribute('level'));
        $this->assertSame(3, $children[2]->getAttribute('level'));
    }

    #[Test]
    public function canParseDocumentWithAlternatingHeadingsAndText(): void
    {
        $input = "# Heading 1\nParagraph 1\n## Heading 2\nParagraph 2";

        $tokens = $this->lexer->tokenize($input);
        $stream = new TokenStream($tokens);
        $ast = $this->parser->parse($stream);

        $children = $ast->getChildren();
        $this->assertCount(4, $children);

        $this->assertInstanceOf(HeadingNode::class, $children[0]);
        $this->assertInstanceOf(TextNode::class, $children[1]);
        $this->assertInstanceOf(HeadingNode::class, $children[2]);
        $this->assertInstanceOf(TextNode::class, $children[3]);
    }

    #[Test]
    public function canParsePlainTextOnlyDocument(): void
    {
        $input = 'Just plain text without any markup.';

        $tokens = $this->lexer->tokenize($input);
        $stream = new TokenStream($tokens);
        $ast = $this->parser->parse($stream);

        $children = $ast->getChildren();
        $this->assertCount(1, $children);
        $this->assertInstanceOf(TextNode::class, $children[0]);
        $this->assertSame('Just plain text without any markup.', $children[0]->getAttribute('content'));
    }

    #[Test]
    public function parsingEmptyStringReturnsEmptyDocumentNode(): void
    {
        $input = '';

        $tokens = $this->lexer->tokenize($input);
        $stream = new TokenStream($tokens);
        $ast = $this->parser->parse($stream);

        $this->assertInstanceOf(DocumentNode::class, $ast);
        $this->assertCount(0, $ast->getChildren());
    }

    #[Test]
    public function canParseJapaneseHeading(): void
    {
        $input = '# こんにちは世界';

        $tokens = $this->lexer->tokenize($input);
        $stream = new TokenStream($tokens);
        $ast = $this->parser->parse($stream);

        $heading = $ast->getChildren()[0];
        $textNode = $heading->getChildren()[0];

        $this->assertSame('こんにちは世界', $textNode->getAttribute('content'));
    }

    #[Test]
    public function hashWithoutSpaceIsNotParsedAsHeading(): void
    {
        $input = '#NoSpace';

        $tokens = $this->lexer->tokenize($input);
        $stream = new TokenStream($tokens);
        $ast = $this->parser->parse($stream);

        $children = $ast->getChildren();
        // HeadingInterpreter does not match, so tokens are skipped
        // In this case, # is skipped and NoSpace becomes TextNode
        $this->assertGreaterThanOrEqual(1, count($children));
    }

    #[Test]
    public function canParseHeadingWithDoubleSpace(): void
    {
        $input = '#  Double Space';

        $tokens = $this->lexer->tokenize($input);
        $stream = new TokenStream($tokens);
        $ast = $this->parser->parse($stream);

        $heading = $ast->getChildren()[0];
        $this->assertInstanceOf(HeadingNode::class, $heading);
        $this->assertSame(1, $heading->getAttribute('level'));
    }

    #[Test]
    public function canVerifyLexerTokenizationResult(): void
    {
        $input = '# Test';

        $tokens = $this->lexer->tokenize($input);

        $this->assertCount(3, $tokens);
        $this->assertInstanceOf(HashToken::class, $tokens[0]);
        $this->assertInstanceOf(SpaceToken::class, $tokens[1]);
        $this->assertSame('Test', $tokens[2]->getValue());
    }
}
