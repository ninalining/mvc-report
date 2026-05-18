<?php

namespace App\Tests\Card;

use App\Card\Card;
use App\Card\CardGraphic;
use App\Card\DeckOfCards;
use PHPUnit\Framework\TestCase;

class DeckOfCardsTest extends TestCase
{
    public function testNewDeckHas52Cards(): void
    {
        $deck = new DeckOfCards();
        $this->assertSame(52, $deck->getNumberOfCards());
    }

    public function testDeckNotShuffledByDefault(): void
    {
        $deck = new DeckOfCards();
        $this->assertFalse($deck->isShuffled());
    }

    public function testShuffle(): void
    {
        $deck = new DeckOfCards();
        $deck->shuffle();
        $this->assertTrue($deck->isShuffled());
    }

    public function testDrawCard(): void
    {
        $deck = new DeckOfCards();
        $card = $deck->drawCard();

        $this->assertInstanceOf(Card::class, $card);
        $this->assertSame(51, $deck->getNumberOfCards());
    }

    public function testDrawCards(): void
    {
        $deck = new DeckOfCards();
        $cards = $deck->drawCards(5);

        $this->assertCount(5, $cards);
        $this->assertSame(47, $deck->getNumberOfCards());
    }

    public function testDrawCardFromEmptyDeck(): void
    {
        $deck = new DeckOfCards();
        $deck->drawCards(52);
        $card = $deck->drawCard();

        $this->assertNull($card);
    }

    public function testDrawCardsMoreThanAvailable(): void
    {
        $deck = new DeckOfCards();
        $deck->drawCards(50);
        $cards = $deck->drawCards(5);

        $this->assertCount(2, $cards);
        $this->assertSame(0, $deck->getNumberOfCards());
    }

    public function testGetCards(): void
    {
        $deck = new DeckOfCards();
        $cards = $deck->getCards();

        $this->assertCount(52, $cards);
        $this->assertInstanceOf(CardGraphic::class, $cards[0]);
    }

    public function testSort(): void
    {
        $deck = new DeckOfCards();
        $deck->shuffle();
        $deck->sort();

        $cards = $deck->getCards();
        // First card should be ♠2 after sorting
        $this->assertSame('♠', $cards[0]->getSuit());
        $this->assertSame('2', $cards[0]->getValue());
    }

    public function testGetGroupedCards(): void
    {
        $deck = new DeckOfCards();
        $grouped = $deck->getGroupedCards();

        $this->assertCount(4, $grouped);
        $this->assertArrayHasKey('♠', $grouped);
        $this->assertArrayHasKey('♥', $grouped);
        $this->assertArrayHasKey('♦', $grouped);
        $this->assertArrayHasKey('♣', $grouped);
        $this->assertCount(13, $grouped['♠']);
    }
}
