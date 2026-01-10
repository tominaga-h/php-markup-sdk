<?php

namespace Hytmng\MarkupSdk\Parser;

use Hytmng\MarkupSdk\Token\TokenInterface;

class TokenStream
{
    /** @var TokenInterface[] */
    private array $tokens;

    private int $cursor = 0;

    public function __construct(array $tokens) {
        $this->tokens = $tokens;
    }

    /**
     * 現在のトークンを取得
     * Get the current token
     *
     * @return TokenInterface|null
     */
    public function current(): ?TokenInterface {
        return $this->tokens[$this->cursor] ?? null;
    }

    /**
     * 次のトークンをのぞき見る（進めない）
     * Peek the next token without advancing the cursor
     *
     * @param int $offset
     * @return TokenInterface|null
     */
    public function peek(int $offset = 1): ?TokenInterface {
        return $this->tokens[$this->cursor + $offset] ?? null;
    }

    /**
     * カーソルを次に進める
     * Advance the cursor to the next token
     */
    public function next(): void {
        $this->cursor++;
    }

    /**
     * 最後まで到達したか
     * Check if the end of the stream has been reached
     *
     * @return bool
     */
    public function isEnd(): bool {
        return $this->cursor >= count($this->tokens);
    }
}
