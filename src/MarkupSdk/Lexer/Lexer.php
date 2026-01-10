<?php

namespace Hytmng\MarkupSdk\Lexer;

use Hytmng\MarkupSdk\Token\TokenInterface;
use Hytmng\MarkupSdk\Token\TextToken;

class Lexer
{
    /** @var TokenInterface[] */
    private array $tokens;

    public function __construct()
    {
        $this->tokens = [];
    }

    /**
     * TokenInterfaceを実装したクラスのインスタンスを登録する
     * Register a token that implements TokenInterface
     *
     * @param TokenInterface $token
     */
    public function registerToken(TokenInterface $token): void
    {
        $this->tokens[] = $token;
    }

    /**
     * TokenInterfaceを実装したクラスのインスタンスの配列を登録する
     * Register an array of tokens that implement TokenInterface
     *
     * @param TokenInterface[] $tokens
     */
    public function registerTokens(array $tokens): void
    {
        foreach ($tokens as $token) {
            if (!$token instanceof TokenInterface) {
                throw new \InvalidArgumentException('トークンはTokenInterfaceを実装したクラスのインスタンスでなければなりません。Token must be an instance of TokenInterface');
            }
            $this->registerToken($token);
        }
    }

    /**
     * 文字列をトークンに分割する
     * Tokenize a string into an array of tokens
     *
     * @param string $input トークンに分割する文字列
     * @return TokenInterface[]
     */
    public function tokenize(string $input): array {
        $tokens = [];
        $offset = 0;
        $length = strlen($input);
        $textBuffer = "";

        while ($offset < $length) {
            $remainingInput = substr($input, $offset);
            $matched = false;

            foreach ($this->tokens as $token) {
                $pattern = $token->getPattern();
                if ($pattern === '') {
                    continue;
                }

                if (preg_match($pattern, $remainingInput, $matches)) {
                    // 記号が見つかる前に溜まっていたテキストをTextTokenとして確定
                    if ($textBuffer !== "") {
                        $tokens[] = $this->createTextToken($textBuffer);
                        $textBuffer = "";
                    }

                    // 既存のトークンクラスのインスタンスを複製して登録
                    $newToken = clone $token;
                    $newToken->setValue($matches[0]);
                    $tokens[] = $newToken;

                    // オフセットを更新
                    $offset += strlen($matches[0]);
                    $matched = true;
                    break;
                }
            }

            if (!$matched) {
                // 記号が見つからなかった場合はテキストバッファに追加
                $textBuffer .= $input[$offset];
                $offset++;
            }
        }

        if ($textBuffer !== "") {
            // 最後に残ったテキストをTextTokenとして確定
            $tokens[] = $this->createTextToken($textBuffer);
        }

        return $tokens;
    }

    private function createTextToken(string $value): TextToken {
        $token = new TextToken();
        $token->setValue($value);
        return $token;
    }
}
