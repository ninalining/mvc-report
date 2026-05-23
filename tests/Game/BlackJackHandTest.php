<?php

namespace App\Tests\Game;

use App\Card\CardGraphic;
use App\Game\BlackJackHand;
use PHPUnit\Framework\TestCase;

class BlackJackHandTest extends TestCase
{
    public function testEmptyHandValue(): void
    {
        $hand = new BlackJackHand();
        $this->assertSame(0, $hand->getValue());
    }

    public function testSimpleCardValues(): void
    {
        $hand = new BlackJackHand();
        $hand->addCard(new CardGraphic('♠', '5'));
        $hand->addCard(new CardGraphic('♥', '3'));
        $this->assertSame(8, $hand->getValue());
    }

    public function testFaceCardsWorthTen(): void
    {
        $hand = new BlackJackHand();
        $hand->addCard(new CardGraphic('♠', 'J'));
        $this->assertSame(10, $hand->getValue());

        $hand->addCard(new CardGraphic('♥', 'Q'));
        $this->assertSame(20, $hand->getValue());

        $hand2 = new BlackJackHand();
        $hand2->addCard(new CardGraphic('♦', 'K'));
        $this->assertSame(10, $hand2->getValue());
    }

    public function testAceAsEleven(): void
    {
        $hand = new BlackJackHand();
        $hand->addCard(new CardGraphic('♠', 'A'));
        $hand->addCard(new CardGraphic('♥', '5'));
        $this->assertSame(16, $hand->getValue());
    }

    public function testAceReducedToOne(): void
    {
        $hand = new BlackJackHand();
        $hand->addCard(new CardGraphic('♠', 'A'));
        $hand->addCard(new CardGraphic('♥', '9'));
        $hand->addCard(new CardGraphic('♦', '5'));
        // 11+9+5=25 -> reduce ace: 1+9+5=15
        $this->assertSame(15, $hand->getValue());
    }

    public function testTwoAces(): void
    {
        $hand = new BlackJackHand();
        $hand->addCard(new CardGraphic('♠', 'A'));
        $hand->addCard(new CardGraphic('♥', 'A'));
        // 11+11=22 -> 1+11=12
        $this->assertSame(12, $hand->getValue());
    }

    public function testIsBlackJack(): void
    {
        $hand = new BlackJackHand();
        $hand->addCard(new CardGraphic('♠', 'A'));
        $hand->addCard(new CardGraphic('♥', 'K'));
        $this->assertTrue($hand->isBlackJack());
        $this->assertSame(21, $hand->getValue());
    }

    public function testIsNotBlackJackWithThreeCards(): void
    {
        $hand = new BlackJackHand();
        $hand->addCard(new CardGraphic('♠', '7'));
        $hand->addCard(new CardGraphic('♥', '7'));
        $hand->addCard(new CardGraphic('♦', '7'));
        // 21 but not blackjack (3 cards)
        $this->assertFalse($hand->isBlackJack());
        $this->assertSame(21, $hand->getValue());
    }

    public function testIsBusted(): void
    {
        $hand = new BlackJackHand();
        $hand->addCard(new CardGraphic('♠', 'K'));
        $hand->addCard(new CardGraphic('♥', 'Q'));
        $hand->addCard(new CardGraphic('♦', '5'));
        $this->assertTrue($hand->isBusted());
    }

    public function testNotBusted(): void
    {
        $hand = new BlackJackHand();
        $hand->addCard(new CardGraphic('♠', 'K'));
        $hand->addCard(new CardGraphic('♥', 'Q'));
        $this->assertFalse($hand->isBusted());
    }

    public function testCanSplitSameValue(): void
    {
        $hand = new BlackJackHand();
        $hand->addCard(new CardGraphic('♠', '8'));
        $hand->addCard(new CardGraphic('♥', '8'));
        $this->assertTrue($hand->canSplit());
    }

    public function testCanSplitFaceCards(): void
    {
        // J and Q both worth 10
        $hand = new BlackJackHand();
        $hand->addCard(new CardGraphic('♠', 'J'));
        $hand->addCard(new CardGraphic('♥', 'Q'));
        $this->assertTrue($hand->canSplit());
    }

    public function testCannotSplitDifferentValues(): void
    {
        $hand = new BlackJackHand();
        $hand->addCard(new CardGraphic('♠', '5'));
        $hand->addCard(new CardGraphic('♥', '8'));
        $this->assertFalse($hand->canSplit());
    }

    public function testCannotSplitThreeCards(): void
    {
        $hand = new BlackJackHand();
        $hand->addCard(new CardGraphic('♠', '8'));
        $hand->addCard(new CardGraphic('♥', '8'));
        $hand->addCard(new CardGraphic('♦', '8'));
        $this->assertFalse($hand->canSplit());
    }

    public function testToArrayShowsCards(): void
    {
        $hand = new BlackJackHand();
        $hand->addCard(new CardGraphic('♠', 'A'));
        $hand->addCard(new CardGraphic('♥', 'K'));

        $data = $hand->toArray();
        $this->assertCount(2, $data['cards']);
        $this->assertSame(21, $data['value']);
        $this->assertTrue($data['blackjack']);
        $this->assertFalse($data['busted']);
    }

    public function testToArrayHidesFirstCard(): void
    {
        $hand = new BlackJackHand();
        $hand->addCard(new CardGraphic('♠', 'A'));
        $hand->addCard(new CardGraphic('♥', 'K'));

        $data = $hand->toArray(true);
        $this->assertTrue($data['cards'][0]['hidden']);
        $this->assertSame('??', $data['cards'][0]['label']);
        $this->assertSame('?', $data['value']);
    }

    public function testToArrayRedCard(): void
    {
        $hand = new BlackJackHand();
        $hand->addCard(new CardGraphic('♥', '5'));

        $data = $hand->toArray();
        $this->assertStringContainsString('red', $data['cards'][0]['cssClass']);
    }

    public function testToArrayBlackCard(): void
    {
        $hand = new BlackJackHand();
        $hand->addCard(new CardGraphic('♠', '5'));

        $data = $hand->toArray();
        $this->assertStringNotContainsString('red', $data['cards'][0]['cssClass']);
    }
}
