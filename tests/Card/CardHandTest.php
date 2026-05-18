<?php

namespace App\Tests\Card;

use App\Card\Card;
use App\Card\CardHand;
use PHPUnit\Framework\TestCase;

class CardHandTest extends TestCase
{
    public function testEmptyHand(): void
    {
        $hand = new CardHand();
        $this->assertSame(0, $hand->getNumberOfCards());
        $this->assertSame([], $hand->getCards());
    }

    public function testAddCard(): void
    {
        $hand = new CardHand();
        $card = new Card('♠', 'A');
        $hand->addCard($card);

        $this->assertSame(1, $hand->getNumberOfCards());
        $this->assertSame([$card], $hand->getCards());
    }

    public function testAddMultipleCards(): void
    {
        $hand = new CardHand();
        $card1 = new Card('♠', 'A');
        $card2 = new Card('♥', 'K');
        $hand->addCard($card1);
        $hand->addCard($card2);

        $this->assertSame(2, $hand->getNumberOfCards());
    }

    public function testToString(): void
    {
        $hand = new CardHand();
        $hand->addCard(new Card('♠', 'A'));
        $hand->addCard(new Card('♥', 'K'));

        $this->assertSame('A♠, K♥', (string) $hand);
    }

    public function testToStringEmpty(): void
    {
        $hand = new CardHand();
        $this->assertSame('', (string) $hand);
    }
}
