<?php

namespace Hytmng\MarkupSdk\Token;

interface TokenInterface {
    /**
     * トークンを抽出するための正規表現パターンを返す（例: "/^#/"）
     * Returns the regular expression pattern for extracting the token (e.g., "/^#/")
     */
    public function getPattern(): string;

    /**
     * トークンの実際の値（文字列）を返す
     * Returns the actual value of the token
     */
    public function getValue(): string;

    /**
     * トークンの実際の値（文字列）を設定する
     * Sets the actual value of the token
     */
    public function setValue(string $value): void;

    /**
     * トークンの説明を返す
     * Returns the description of the token
     */
    public function getDescription(): string;

}
