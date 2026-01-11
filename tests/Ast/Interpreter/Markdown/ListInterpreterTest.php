<?php

declare(strict_types=1);

namespace Tests\Ast\Interpreter\Markdown;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Hytmng\MarkupSdk\Parser\Parser;
use Hytmng\MarkupSdk\Parser\ParserContext;
use Hytmng\MarkupSdk\Parser\TokenStream;
use Hytmng\MarkupSdk\Token\AsteriskToken;
use Hytmng\MarkupSdk\Token\HyphenToken;
use Hytmng\MarkupSdk\Token\SpaceToken;
use Hytmng\MarkupSdk\Token\TextToken;
use Hytmng\MarkupSdk\Token\NewlineToken;
use Hytmng\MarkupSdk\Ast\Node\Markdown\ListNode;
use Hytmng\MarkupSdk\Ast\Node\Markdown\ListItemNode;
use Hytmng\MarkupSdk\Ast\Interpreter\Markdown\ListInterpreter;
use Hytmng\MarkupSdk\Ast\Interpreter\TextInterpreter;

class ListInterpreterTest extends TestCase
{
    private function createAsteriskToken(): AsteriskToken
    {
        $token = new AsteriskToken();
        $token->setValue('*');
        return $token;
    }

    private function createHyphenToken(): HyphenToken
    {
        $token = new HyphenToken();
        $token->setValue('-');
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
        $interpreter = new ListInterpreter();
        $this->assertSame(100, $interpreter->getPriority());
    }

    #[Test]
    public function canInterpretReturnsTrueWhenAsteriskFollowedBySpace(): void
    {
        $interpreter = new ListInterpreter();

        $tokens = [
            $this->createAsteriskToken(),
            $this->createSpaceToken(),
            $this->createTextToken('Item'),
        ];
        $stream = new TokenStream($tokens);

        $this->assertTrue($interpreter->canInterpret($stream));
    }

    #[Test]
    public function canInterpretReturnsTrueWhenHyphenFollowedBySpace(): void
    {
        $interpreter = new ListInterpreter();

        $tokens = [
            $this->createHyphenToken(),
            $this->createSpaceToken(),
            $this->createTextToken('Item'),
        ];
        $stream = new TokenStream($tokens);

        $this->assertTrue($interpreter->canInterpret($stream));
    }

    #[Test]
    public function canInterpretReturnsFalseWhenNoSpaceAfterAsterisk(): void
    {
        $interpreter = new ListInterpreter();

        $tokens = [
            $this->createAsteriskToken(),
            $this->createTextToken('NoSpace'),
        ];
        $stream = new TokenStream($tokens);

        $this->assertFalse($interpreter->canInterpret($stream));
    }

    #[Test]
    public function canInterpretReturnsFalseWhenNoSpaceAfterHyphen(): void
    {
        $interpreter = new ListInterpreter();

        $tokens = [
            $this->createHyphenToken(),
            $this->createTextToken('NoSpace'),
        ];
        $stream = new TokenStream($tokens);

        $this->assertFalse($interpreter->canInterpret($stream));
    }

    #[Test]
    public function canInterpretReturnsFalseWhenNotStartingWithMarker(): void
    {
        $interpreter = new ListInterpreter();

        $tokens = [
            $this->createTextToken('Not a list'),
        ];
        $stream = new TokenStream($tokens);

        $this->assertFalse($interpreter->canInterpret($stream));
    }

    #[Test]
    public function canParseSingleItemWithAsterisk(): void
    {
        $parser = new Parser();
        $parser->registerInterpreters([
            new ListInterpreter(),
            new TextInterpreter(),
        ]);

        $tokens = [
            $this->createAsteriskToken(),
            $this->createSpaceToken(),
            $this->createTextToken('Item 1'),
        ];
        $stream = new TokenStream($tokens);
        $context = new ParserContext($parser, $stream);

        $interpreter = new ListInterpreter();
        $node = $interpreter->interpret($stream, $context);

        $this->assertInstanceOf(ListNode::class, $node);
        $this->assertSame('list', $node->getType());

        $children = $node->getChildren();
        $this->assertCount(1, $children);
        $this->assertInstanceOf(ListItemNode::class, $children[0]);
    }

    #[Test]
    public function canParseSingleItemWithHyphen(): void
    {
        $parser = new Parser();
        $parser->registerInterpreters([
            new ListInterpreter(),
            new TextInterpreter(),
        ]);

        $tokens = [
            $this->createHyphenToken(),
            $this->createSpaceToken(),
            $this->createTextToken('Item 1'),
        ];
        $stream = new TokenStream($tokens);
        $context = new ParserContext($parser, $stream);

        $interpreter = new ListInterpreter();
        $node = $interpreter->interpret($stream, $context);

        $this->assertInstanceOf(ListNode::class, $node);

        $children = $node->getChildren();
        $this->assertCount(1, $children);
        $this->assertInstanceOf(ListItemNode::class, $children[0]);
    }

    #[Test]
    public function canParseMultipleItems(): void
    {
        $parser = new Parser();
        $parser->registerInterpreters([
            new ListInterpreter(),
            new TextInterpreter(),
        ]);

        $tokens = [
            $this->createAsteriskToken(),
            $this->createSpaceToken(),
            $this->createTextToken('Item 1'),
            $this->createNewlineToken(),
            $this->createAsteriskToken(),
            $this->createSpaceToken(),
            $this->createTextToken('Item 2'),
            $this->createNewlineToken(),
            $this->createAsteriskToken(),
            $this->createSpaceToken(),
            $this->createTextToken('Item 3'),
        ];
        $stream = new TokenStream($tokens);
        $context = new ParserContext($parser, $stream);

        $interpreter = new ListInterpreter();
        $node = $interpreter->interpret($stream, $context);

        $this->assertInstanceOf(ListNode::class, $node);

        $children = $node->getChildren();
        $this->assertCount(3, $children);

        foreach ($children as $child) {
            $this->assertInstanceOf(ListItemNode::class, $child);
        }
    }

    #[Test]
    public function listItemContainsTextAsChild(): void
    {
        $parser = new Parser();
        $parser->registerInterpreters([
            new ListInterpreter(),
            new TextInterpreter(),
        ]);

        $tokens = [
            $this->createAsteriskToken(),
            $this->createSpaceToken(),
            $this->createTextToken('My Item'),
        ];
        $stream = new TokenStream($tokens);
        $context = new ParserContext($parser, $stream);

        $interpreter = new ListInterpreter();
        $node = $interpreter->interpret($stream, $context);

        $listItems = $node->getChildren();
        $this->assertCount(1, $listItems);

        $itemChildren = $listItems[0]->getChildren();
        $this->assertCount(1, $itemChildren);
        $this->assertSame('text', $itemChildren[0]->getType());
        $this->assertSame('My Item', $itemChildren[0]->getAttribute('content'));
    }

    #[Test]
    public function listParsingStopsAtNonListLine(): void
    {
        $parser = new Parser();
        $parser->registerInterpreters([
            new ListInterpreter(),
            new TextInterpreter(),
        ]);

        $tokens = [
            $this->createAsteriskToken(),
            $this->createSpaceToken(),
            $this->createTextToken('Item 1'),
            $this->createNewlineToken(),
            $this->createTextToken('Not a list item'),
        ];
        $stream = new TokenStream($tokens);
        $context = new ParserContext($parser, $stream);

        $interpreter = new ListInterpreter();
        $node = $interpreter->interpret($stream, $context);

        // List should only have 1 item
        $children = $node->getChildren();
        $this->assertCount(1, $children);

        // Stream should be at "Not a list item" position
        $this->assertInstanceOf(TextToken::class, $stream->current());
        $this->assertSame('Not a list item', $stream->current()->getValue());
    }

    #[Test]
    public function canParseMixedMarkers(): void
    {
        $parser = new Parser();
        $parser->registerInterpreters([
            new ListInterpreter(),
            new TextInterpreter(),
        ]);

        $tokens = [
            $this->createAsteriskToken(),
            $this->createSpaceToken(),
            $this->createTextToken('Item with asterisk'),
            $this->createNewlineToken(),
            $this->createHyphenToken(),
            $this->createSpaceToken(),
            $this->createTextToken('Item with hyphen'),
        ];
        $stream = new TokenStream($tokens);
        $context = new ParserContext($parser, $stream);

        $interpreter = new ListInterpreter();
        $node = $interpreter->interpret($stream, $context);

        $children = $node->getChildren();
        $this->assertCount(2, $children);
    }

    #[Test]
    public function toHtmlRendersCorrectly(): void
    {
        $parser = new Parser();
        $parser->registerInterpreters([
            new ListInterpreter(),
            new TextInterpreter(),
        ]);

        $tokens = [
            $this->createAsteriskToken(),
            $this->createSpaceToken(),
            $this->createTextToken('Item 1'),
            $this->createNewlineToken(),
            $this->createAsteriskToken(),
            $this->createSpaceToken(),
            $this->createTextToken('Item 2'),
        ];
        $stream = new TokenStream($tokens);
        $context = new ParserContext($parser, $stream);

        $interpreter = new ListInterpreter();
        $node = $interpreter->interpret($stream, $context);

        $expected = "<ul>\n<li>Item 1</li>\n<li>Item 2</li>\n</ul>\n";
        $this->assertSame($expected, $node->toHtml());
    }
}

