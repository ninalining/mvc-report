<?php

namespace App\Tests\Card;

use App\Card\Card;
use PHPUnit\Framework\TestCase;

class CardTest extends TestCase
{
    public function testConstructorAndGetters(): void
    {
        $card = new Card('♠', 'A');
        $this->assertSame('♠', $card->getSuit());
        $this->assertSame('A', $card->getValue());
    }

    public function testToString(): void
    {
        $card = new Card('♥', 'K');
        $this->assertSame('K♥', (string) $card);
    }

    public function testMagicGetSuit(): void
    {
        $card = new Card('♦', '10');
        $this->assertSame('♦', $card->suit);
    }

    public function testMagicGetValue(): void
    {
        $card = new Card('♣', '7');
        $this->assertSame('7', $card->value);
    }

    public function testMagicGetInvalidProperty(): void
    {
        $this->expectException(\Exception::class);
        $card = new Card('♠', '2');
        $card->invalid;
    }
}
