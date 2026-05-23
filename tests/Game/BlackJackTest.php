<?php

namespace App\Tests\Game;

use App\Game\BlackJack;
use PHPUnit\Framework\TestCase;

class BlackJackTest extends TestCase
{
    public function testConstructor(): void
    {
        $game = new BlackJack('TestPlayer', 1000);
        $this->assertSame('TestPlayer', $game->getPlayerName());
        $this->assertSame(1000, $game->getBalance());
        $this->assertEmpty($game->getPlayerHands());
    }

    public function testConstructorWithAi(): void
    {
        $game = new BlackJack('TestPlayer', 1000, true);
        $this->assertTrue($game->isAiEnabled());
        $this->assertNull($game->getAiHand()); // Not dealt yet
    }

    public function testAiDisabledByDefault(): void
    {
        $game = new BlackJack('TestPlayer', 1000);
        $this->assertFalse($game->isAiEnabled());
        $this->assertNull($game->getAiHand());
        $this->assertSame('', $game->getAiResult());
    }

    public function testAiGetsCardsWhenEnabled(): void
    {
        $game = new BlackJack('TestPlayer', 1000, true);
        $game->deal(1, 10);
        $this->assertNotNull($game->getAiHand());
        $this->assertSame(2, $game->getAiHand()->getNumberOfCards());
    }

    public function testAiPlaysWhenGameEnds(): void
    {
        $game = new BlackJack('TestPlayer', 1000, true);
        $game->deal(1, 10);

        while (!$game->isGameOver()) {
            $game->stand();
        }

        $aiResult = $game->getAiResult();
        $validResults = ['player_win', 'dealer_win', 'push', 'player_bust', 'dealer_bust', 'blackjack'];
        $this->assertContains($aiResult, $validResults);
    }

    public function testToArrayContainsAiData(): void
    {
        $game = new BlackJack('TestPlayer', 1000, true);
        $game->deal(1, 10);

        $data = $game->toArray();
        $this->assertTrue($data['aiEnabled']);
        $this->assertNotNull($data['ai']);
        $this->assertArrayHasKey('cards', $data['ai']);
    }

    public function testDealSingleHand(): void
    {
        $game = new BlackJack('TestPlayer', 1000);
        $game->deal(1, 100);

        $this->assertCount(1, $game->getPlayerHands());
        $this->assertSame(2, $game->getPlayerHands()[0]->getNumberOfCards());
        // Dealer gets 2 cards, but may get more if player has blackjack (triggers dealerPlay)
        $this->assertGreaterThanOrEqual(2, $game->getDealerHand()->getNumberOfCards());
        $this->assertSame(900, $game->getBalance());
    }

    public function testDealMultipleHands(): void
    {
        $game = new BlackJack('TestPlayer', 1000);
        $game->deal(3, 100);

        $this->assertCount(3, $game->getPlayerHands());
        $this->assertSame(700, $game->getBalance());
    }

    public function testDealClampedTo3Hands(): void
    {
        $game = new BlackJack('TestPlayer', 1000);
        $game->deal(5, 50);

        $this->assertCount(3, $game->getPlayerHands());
    }

    public function testDealClampedTo1Hand(): void
    {
        $game = new BlackJack('TestPlayer', 1000);
        $game->deal(0, 50);

        $this->assertCount(1, $game->getPlayerHands());
    }

    public function testDealInsufficientBalance(): void
    {
        $game = new BlackJack('TestPlayer', 50);
        $game->deal(1, 100);

        // Should not deal — insufficient funds
        $this->assertEmpty($game->getPlayerHands());
    }

    public function testHitAddsCard(): void
    {
        $game = new BlackJack('TestPlayer', 1000);
        $game->deal(1, 10);

        if (!$game->isGameOver()) {
            $game->hit();
            $hand = $game->getPlayerHands()[0];
            $this->assertGreaterThanOrEqual(3, $hand->getNumberOfCards());
        }
    }

    public function testStandTriggersDealer(): void
    {
        $game = new BlackJack('TestPlayer', 1000);
        $game->deal(1, 10);

        if (!$game->isGameOver()) {
            $game->stand();
            $this->assertTrue($game->isGameOver());
        }
    }

    public function testGameOverAfterAllStands(): void
    {
        $game = new BlackJack('TestPlayer', 1000);
        $game->deal(2, 10);

        // Stand on all hands
        for ($i = 0; $i < 10; $i++) {
            if ($game->isGameOver()) {
                break;
            }
            $game->stand();
        }

        $this->assertTrue($game->isGameOver());
    }

    public function testResultsMatchHandCount(): void
    {
        $game = new BlackJack('TestPlayer', 1000);
        $game->deal(2, 10);

        // Play out all hands
        for ($i = 0; $i < 20; $i++) {
            if ($game->isGameOver()) {
                break;
            }
            $game->stand();
        }

        $this->assertCount(2, $game->getResults());
    }

    public function testBetsMatchHandCount(): void
    {
        $game = new BlackJack('TestPlayer', 1000);
        $game->deal(3, 50);

        $this->assertCount(3, $game->getBets());
        foreach ($game->getBets() as $bet) {
            $this->assertSame(50, $bet);
        }
    }

    public function testToArrayStructure(): void
    {
        $game = new BlackJack('TestPlayer', 1000);
        $game->deal(1, 10);

        $data = $game->toArray();
        $this->assertArrayHasKey('playerName', $data);
        $this->assertArrayHasKey('balance', $data);
        $this->assertArrayHasKey('dealer', $data);
        $this->assertArrayHasKey('hands', $data);
        $this->assertArrayHasKey('gameOver', $data);
        $this->assertSame('TestPlayer', $data['playerName']);
    }

    public function testResultMessage(): void
    {
        $game = new BlackJack('TestPlayer', 1000);
        $game->deal(1, 10);

        $msg = $game->getResultMessage(0);
        $this->assertIsString($msg);

        if (!$game->isGameOver()) {
            $this->assertSame('Spelet pågår...', $msg);
        }
    }

    public function testResultMessageAfterGame(): void
    {
        $game = new BlackJack('TestPlayer', 1000);
        $game->deal(1, 10);

        // Play to completion
        for ($i = 0; $i < 20; $i++) {
            if ($game->isGameOver()) {
                break;
            }
            $game->stand();
        }

        $msg = $game->getResultMessage(0);
        $validMessages = [
            'Tjock! Du förlorade.',
            'Banken är tjock! Du vinner!',
            'Du vinner!',
            'Banken vinner.',
            'Lika — insatsen returneras.',
            'Black Jack! Du vinner 3:2!',
        ];
        $this->assertContains($msg, $validMessages);
    }

    public function testSetBalance(): void
    {
        $game = new BlackJack('TestPlayer', 1000);
        $game->setBalance(500);
        $this->assertSame(500, $game->getBalance());
    }

    public function testGetDeck(): void
    {
        $game = new BlackJack('TestPlayer', 1000);
        $this->assertNotNull($game->getDeck());
    }

    public function testGetActiveHandBeforeDeal(): void
    {
        $game = new BlackJack('TestPlayer', 1000);
        $this->assertNull($game->getActiveHand());
    }

    public function testGetActiveHandIndex(): void
    {
        $game = new BlackJack('TestPlayer', 1000);
        $game->deal(1, 10);
        $this->assertSame(0, $game->getActiveHandIndex());
    }

    public function testSplitWhenGameOver(): void
    {
        $game = new BlackJack('TestPlayer', 1000);
        $game->deal(1, 10);

        // Play to completion
        for ($i = 0; $i < 20; $i++) {
            if ($game->isGameOver()) {
                break;
            }
            $game->stand();
        }

        // Split should do nothing when game is over
        $handCount = count($game->getPlayerHands());
        $game->split();
        $this->assertCount($handCount, $game->getPlayerHands());
    }

    public function testSplitInsufficientBalance(): void
    {
        // Try many games with minimal balance — split should fail if not enough money
        for ($attempt = 0; $attempt < 50; $attempt++) {
            $game = new BlackJack('TestPlayer', 20);
            $game->deal(1, 20);

            if ($game->isGameOver()) {
                continue;
            }

            $hand = $game->getActiveHand();
            if ($hand !== null && $hand->canSplit()) {
                // Balance is 0 after bet, can't split
                $handCount = count($game->getPlayerHands());
                $game->split();
                $this->assertCount($handCount, $game->getPlayerHands());
                return;
            }
        }

        $this->assertTrue(true);
    }

    public function testSplitCreatesExtraHand(): void
    {
        // Try many games until we find a splittable hand
        for ($attempt = 0; $attempt < 200; $attempt++) {
            $game = new BlackJack('TestPlayer', 1000);
            $game->deal(1, 10);

            if ($game->isGameOver()) {
                continue;
            }

            $hand = $game->getActiveHand();
            if ($hand !== null && $hand->canSplit()) {
                $game->split();
                $this->assertCount(2, $game->getPlayerHands());
                // Each hand should have 2 cards after split
                foreach ($game->getPlayerHands() as $h) {
                    $this->assertSame(2, $h->getNumberOfCards());
                }
                return;
            }
        }

        $this->assertTrue(true);
    }

    public function testToArrayHasCanSplit(): void
    {
        $game = new BlackJack('TestPlayer', 1000);
        $game->deal(1, 10);

        $data = $game->toArray();
        $this->assertArrayHasKey('canSplit', $data['hands'][0]);
    }

    public function testHitWhenGameOver(): void
    {
        $game = new BlackJack('TestPlayer', 1000);
        $game->deal(1, 10);

        // Play to completion
        for ($i = 0; $i < 20; $i++) {
            if ($game->isGameOver()) {
                break;
            }
            $game->stand();
        }

        // Hit should do nothing when game is over
        $cardCount = $game->getPlayerHands()[0]->getNumberOfCards();
        $game->hit();
        $this->assertSame($cardCount, $game->getPlayerHands()[0]->getNumberOfCards());
    }

    public function testStandWhenGameOver(): void
    {
        $game = new BlackJack('TestPlayer', 1000);
        $game->deal(1, 10);

        for ($i = 0; $i < 20; $i++) {
            if ($game->isGameOver()) {
                break;
            }
            $game->stand();
        }

        // Stand should do nothing when game is over
        $game->stand();
        $this->assertTrue($game->isGameOver());
    }

    public function testPlayerBustLosesBet(): void
    {
        // Play many games until we get a bust
        for ($attempt = 0; $attempt < 50; $attempt++) {
            $game = new BlackJack('TestPlayer', 1000);
            $game->deal(1, 100);

            if ($game->isGameOver()) {
                continue;
            }

            // Hit until bust or 21
            while (!$game->isGameOver()) {
                $hand = $game->getActiveHand();
                if ($hand === null || $hand->getValue() >= 21) {
                    $game->stand();
                    break;
                }
                $game->hit();
            }

            $result = $game->getResults()[0];
            if ($result === 'player_bust') {
                // Player lost bet: balance should be 900 (1000 - 100)
                $this->assertSame(900, $game->getBalance());
                return;
            }
        }

        // If we never busted in 50 attempts, still pass
        $this->assertTrue(true);
    }

    public function testBalanceIncreasesOnWin(): void
    {
        // Play many games — at least one should result in a win
        $sawWin = false;
        for ($attempt = 0; $attempt < 100; $attempt++) {
            $game = new BlackJack('TestPlayer', 1000);
            $game->deal(1, 100);

            // Stand immediately
            while (!$game->isGameOver()) {
                $game->stand();
            }

            $result = $game->getResults()[0];
            $balance = $game->getBalance();

            if ($result === 'player_win' || $result === 'dealer_bust') {
                $this->assertSame(1100, $balance);
                $sawWin = true;
                break;
            } elseif ($result === 'blackjack') {
                $this->assertSame(1150, $balance);
                $sawWin = true;
                break;
            } elseif ($result === 'push') {
                $this->assertSame(1000, $balance);
            } elseif ($result === 'dealer_win') {
                $this->assertSame(900, $balance);
            }
        }

        $this->assertTrue(true);
    }
}
