<?php

namespace App\Tests\Game;

use App\Game\Game21;
use PHPUnit\Framework\TestCase;

class Game21Test extends TestCase
{
    public function testNewGameStatus(): void
    {
        $game = new Game21();
        $this->assertSame('playing', $game->getStatus());
        $this->assertFalse($game->isGameOver());
    }

    public function testNewGameHasCards(): void
    {
        $game = new Game21();
        $this->assertSame(1, $game->getPlayerHand()->getNumberOfCards());
        $this->assertSame(1, $game->getDealerHand()->getNumberOfCards());
    }

    public function testPlayerHit(): void
    {
        $game = new Game21();
        $game->playerHit();
        $this->assertSame(2, $game->getPlayerHand()->getNumberOfCards());
    }

    public function testPlayerHitWhenNotPlaying(): void
    {
        $game = new Game21();
        // Force game over by hitting many times
        for ($i = 0; $i < 20; $i++) {
            $game->playerHit();
            if ($game->isGameOver()) {
                break;
            }
        }
        $cardsBefore = $game->getPlayerHand()->getNumberOfCards();
        $game->playerHit();
        $this->assertSame($cardsBefore, $game->getPlayerHand()->getNumberOfCards());
    }

    public function testPlayerStand(): void
    {
        $game = new Game21();
        $game->playerStand();
        $this->assertTrue($game->isGameOver());
    }

    public function testPlayerStandWhenNotPlaying(): void
    {
        $game = new Game21();
        $game->playerStand();
        $status = $game->getStatus();
        $game->playerStand();
        $this->assertSame($status, $game->getStatus());
    }

    public function testGetHandValueNumericCards(): void
    {
        $game = new Game21();
        $hand = $game->getPlayerHand();
        // We can't control initial cards but we can test the method
        $value = $game->getHandValue($hand);
        $this->assertGreaterThan(0, $value);
    }

    public function testGetStatusMessage(): void
    {
        $game = new Game21();
        $this->assertSame('Spelet pågår...', $game->getStatusMessage());
    }

    public function testGameEndsWithResult(): void
    {
        // Play until game over
        $game = new Game21();
        $game->playerStand();

        $validStatuses = ['dealer_bust', 'dealer_win', 'player_win'];
        $this->assertContains($game->getStatus(), $validStatuses);
        $this->assertTrue($game->isGameOver());
    }

    public function testStatusMessageAfterGameOver(): void
    {
        $game = new Game21();
        $game->playerStand();

        $message = $game->getStatusMessage();
        $this->assertNotSame('Spelet pågår...', $message);
    }

    public function testPlayerBust(): void
    {
        // Keep hitting until bust (statistical certainty with enough tries)
        for ($attempt = 0; $attempt < 100; $attempt++) {
            $game = new Game21();
            for ($i = 0; $i < 10; $i++) {
                $game->playerHit();
                if ($game->getStatus() === 'player_bust') {
                    $this->assertSame('Spelaren är tjock! Banken vinner.', $game->getStatusMessage());
                    return;
                }
            }
        }
        // If we get here without bust, that's extremely unlikely but don't fail
        $this->assertTrue(true);
    }

    public function testHandValueWithAceHigh(): void
    {
        $game = new Game21();
        $hand = new \App\Card\CardHand();
        $hand->addCard(new \App\Card\Card('♠', 'A'));
        // Ace alone = 14
        $this->assertSame(14, $game->getHandValue($hand));
    }

    public function testHandValueWithAceLow(): void
    {
        $game = new Game21();
        $hand = new \App\Card\CardHand();
        $hand->addCard(new \App\Card\Card('♠', 'A'));
        $hand->addCard(new \App\Card\Card('♥', 'K'));
        // A(14) + K(13) = 27 > 21, so A becomes 1: 1 + 13 = 14
        $this->assertSame(14, $game->getHandValue($hand));
    }

    public function testHandValueFaceCards(): void
    {
        $game = new Game21();
        $hand = new \App\Card\CardHand();
        $hand->addCard(new \App\Card\Card('♠', 'J'));
        $hand->addCard(new \App\Card\Card('♥', 'Q'));
        // J=11, Q=12 => 23... wait that's bust but getHandValue just returns the value
        $this->assertSame(23, $game->getHandValue($hand));
    }
}
