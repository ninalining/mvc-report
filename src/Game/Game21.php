<?php

namespace App\Game;

use App\Card\CardHand;
use App\Card\DeckOfCards;

class Game21
{
    private DeckOfCards $deck;
    private CardHand $player;
    private CardHand $dealer;
    private string $status;

    public function __construct()
    {
        $this->deck = new DeckOfCards();
        $this->deck->shuffle();
        $this->player = new CardHand();
        $this->dealer = new CardHand();
        $this->status = 'playing';

        $this->player->addCard($this->deck->drawCard());
        $this->dealer->addCard($this->deck->drawCard());
    }

    public function playerHit(): void
    {
        if ($this->status !== 'playing') {
            return;
        }

        $card = $this->deck->drawCard();
        if ($card !== null) {
            $this->player->addCard($card);
        }

        if ($this->getHandValue($this->player) > 21) {
            $this->status = 'player_bust';
        }
    }

    public function playerStand(): void
    {
        if ($this->status !== 'playing') {
            return;
        }

        $this->dealerPlay();
    }

    private function dealerPlay(): void
    {
        while ($this->getHandValue($this->dealer) < 17) {
            $card = $this->deck->drawCard();
            if ($card === null) {
                break;
            }
            $this->dealer->addCard($card);
        }

        if ($this->getHandValue($this->dealer) > 21) {
            $this->status = 'dealer_bust';
            return;
        }

        $this->compareHands();
    }

    private function compareHands(): void
    {
        $playerValue = $this->getHandValue($this->player);
        $dealerValue = $this->getHandValue($this->dealer);

        if ($dealerValue >= $playerValue) {
            $this->status = 'dealer_win';
            return;
        }

        $this->status = 'player_win';
    }

    public function getHandValue(CardHand $hand): int
    {
        $total = 0;
        $aces = 0;

        foreach ($hand->getCards() as $card) {
            $value = $card->getValue();
            $points = match (true) {
                $value === 'A' => 14,
                $value === 'K' => 13,
                $value === 'Q' => 12,
                $value === 'J' => 11,
                default => (int) $value,
            };

            if ($value === 'A') {
                $aces++;
            }

            $total += $points;
        }

        while ($total > 21 && $aces > 0) {
            $total -= 13; // 14 -> 1
            $aces--;
        }

        return $total;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getPlayerHand(): CardHand
    {
        return $this->player;
    }

    public function getDealerHand(): CardHand
    {
        return $this->dealer;
    }

    public function isGameOver(): bool
    {
        return $this->status !== 'playing';
    }

    public function getStatusMessage(): string
    {
        return match ($this->status) {
            'player_bust' => 'Spelaren är tjock! Banken vinner.',
            'dealer_bust' => 'Banken är tjock! Spelaren vinner.',
            'player_win' => 'Spelaren vinner!',
            'dealer_win' => 'Banken vinner!',
            default => 'Spelet pågår...',
        };
    }
}
