<?php

namespace App\Tests\Game;

use App\Card\CardGraphic;
use App\Game\BlackJackAI;
use App\Game\BlackJackHand;
use PHPUnit\Framework\TestCase;

class BlackJackAITest extends TestCase
{
    private BlackJackAI $ai;

    protected function setUp(): void
    {
        $this->ai = new BlackJackAI();
    }

    public function testStandOn17OrMore(): void
    {
        $hand = new BlackJackHand();
        $hand->addCard(new CardGraphic('♠', 'K'));
        $hand->addCard(new CardGraphic('♥', '7'));
        // 17 - should stand
        $this->assertSame('stand', $this->ai->decide($hand, 10));
    }

    public function testStandOn20(): void
    {
        $hand = new BlackJackHand();
        $hand->addCard(new CardGraphic('♠', 'K'));
        $hand->addCard(new CardGraphic('♥', 'Q'));
        $this->assertSame('stand', $this->ai->decide($hand, 6));
    }

    public function testHitOn8OrLess(): void
    {
        $hand = new BlackJackHand();
        $hand->addCard(new CardGraphic('♠', '3'));
        $hand->addCard(new CardGraphic('♥', '5'));
        // 8 - should hit
        $this->assertSame('hit', $this->ai->decide($hand, 10));
    }

    public function testHitOnLowValue(): void
    {
        $hand = new BlackJackHand();
        $hand->addCard(new CardGraphic('♠', '2'));
        $hand->addCard(new CardGraphic('♥', '3'));
        // 5 - should hit
        $this->assertSame('hit', $this->ai->decide($hand, 5));
    }

    public function testStand12To16VsDealerWeak(): void
    {
        // 14 vs dealer 5 (weak) - should stand
        $hand = new BlackJackHand();
        $hand->addCard(new CardGraphic('♠', '7'));
        $hand->addCard(new CardGraphic('♥', '7'));
        $this->assertSame('stand', $this->ai->decide($hand, 5));
    }

    public function testHit12To16VsDealerStrong(): void
    {
        // 14 vs dealer 10 (strong) - should hit
        $hand = new BlackJackHand();
        $hand->addCard(new CardGraphic('♠', '7'));
        $hand->addCard(new CardGraphic('♥', '7'));
        $this->assertSame('hit', $this->ai->decide($hand, 10));
    }

    public function testHit9To11(): void
    {
        // 10 - should hit
        $hand = new BlackJackHand();
        $hand->addCard(new CardGraphic('♠', '4'));
        $hand->addCard(new CardGraphic('♥', '6'));
        $this->assertSame('hit', $this->ai->decide($hand, 7));
    }

    public function testStand13VsDealer2(): void
    {
        $hand = new BlackJackHand();
        $hand->addCard(new CardGraphic('♠', '6'));
        $hand->addCard(new CardGraphic('♥', '7'));
        // 13 vs dealer 2 (weak) - stand
        $this->assertSame('stand', $this->ai->decide($hand, 2));
    }

    public function testHit16VsDealer7(): void
    {
        $hand = new BlackJackHand();
        $hand->addCard(new CardGraphic('♠', '9'));
        $hand->addCard(new CardGraphic('♥', '7'));
        // 16 vs dealer 7 (strong) - hit
        $this->assertSame('hit', $this->ai->decide($hand, 7));
    }

    public function testGetDealerUpCardValue(): void
    {
        $dealerHand = new BlackJackHand();
        $dealerHand->addCard(new CardGraphic('♠', '3')); // hidden
        $dealerHand->addCard(new CardGraphic('♥', 'K')); // visible

        $this->assertSame(10, $this->ai->getDealerUpCardValue($dealerHand));
    }

    public function testGetDealerUpCardValueAce(): void
    {
        $dealerHand = new BlackJackHand();
        $dealerHand->addCard(new CardGraphic('♠', '5')); // hidden
        $dealerHand->addCard(new CardGraphic('♥', 'A')); // visible

        $this->assertSame(11, $this->ai->getDealerUpCardValue($dealerHand));
    }

    public function testGetDealerUpCardValueNumber(): void
    {
        $dealerHand = new BlackJackHand();
        $dealerHand->addCard(new CardGraphic('♠', '5')); // hidden
        $dealerHand->addCard(new CardGraphic('♥', '7')); // visible

        $this->assertSame(7, $this->ai->getDealerUpCardValue($dealerHand));
    }

    public function testGetDealerUpCardValueEmptyHand(): void
    {
        $dealerHand = new BlackJackHand();
        $this->assertSame(0, $this->ai->getDealerUpCardValue($dealerHand));
    }
}
